<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Poli;
use App\Models\JadwalPeriksa;
use App\Models\DaftarPoli;
use App\Models\Periksa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PoliklinikRegressionTest extends TestCase
{
    use RefreshDatabase;

    private int $nomorUser = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Tidak memerlukan Vite build saat testing.
        $this->withoutVite();
    }

    /**
     * Membuat akun dummy khusus database testing.
     */
    private function buatUser(
        string $role,
        ?int $idPoli = null
    ): User {
        $this->nomorUser++;

        return User::create([
            'nama' => 'Pengguna ' . $role . ' ' . $this->nomorUser,
            'email' => 'uji-' . $role . '-' .
                $this->nomorUser . '@example.test',
            'password' => 'Password123!',
            'role' => $role,
            'id_poli' => $idPoli,
        ]);
    }

    /**
     * Menyiapkan poli, dokter, jadwal, dan pasien.
     */
    private function buatDataUji(): array
    {
        $poli = Poli::create([
            'nama_poli' => 'Poli Umum Testing',
            'keterangan' => 'Data pengujian',
        ]);

        $dokter = $this->buatUser('dokter', $poli->id);

        $pasien = $this->buatUser('pasien');

        $jadwal = JadwalPeriksa::create([
            'id_dokter' => $dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
            'status' => 1,
        ]);

        return compact(
            'poli',
            'dokter',
            'pasien',
            'jadwal'
        );
    }

    /**
     * TEST 1
     * Pengguna yang belum login harus diarahkan ke login.
     */
    public function test_guest_tidak_bisa_mengakses_halaman_role(): void
    {
        $this->get(route('obat.index'))
            ->assertRedirect(route('login'));

        $this->get(route('periksa-pasien.index'))
            ->assertRedirect(route('login'));

        $this->get(route('pasien.daftar'))
            ->assertRedirect(route('login'));
    }

    /**
     * TEST 2
     * Setiap role hanya dapat mengakses halaman miliknya.
     */
    public function test_hak_akses_admin_dokter_pasien(): void
    {
        $admin = $this->buatUser('admin');
        $dokter = $this->buatUser('dokter');
        $pasien = $this->buatUser('pasien');

        $pengguna = [
            'admin' => $admin,
            'dokter' => $dokter,
            'pasien' => $pasien,
        ];

        $halaman = [
            'admin' => 'obat.index',
            'dokter' => 'periksa-pasien.index',
            'pasien' => 'pasien.daftar',
        ];

        foreach ($pengguna as $role => $user) {
            foreach ($halaman as $pemilik => $route) {
                $response = $this->actingAs($user)
                    ->get(route($route));

                if ($role === $pemilik) {
                    $response->assertOk();
                } else {
                    $response->assertForbidden();
                }
            }
        }
    }

    /**
     * TEST 3
     * Pasien tidak boleh memalsukan ID pasien lain.
     */
    public function test_pendaftaran_menggunakan_akun_login(): void
    {
        $data = $this->buatDataUji();

        $pasienLain = $this->buatUser('pasien');

        $response = $this->actingAs($data['pasien'])
            ->post(route('pasien.daftar.submit'), [
                'id_pasien' => $pasienLain->id,
                'id_poli' => $data['poli']->id,
                'id_jadwal' => $data['jadwal']->id,
                'keluhan' => 'Demam',
            ]);

        $response->assertRedirect(
            route('pasien.daftar')
        );

        $this->assertDatabaseHas('daftar_poli', [
            'id_pasien' => $data['pasien']->id,
            'id_jadwal' => $data['jadwal']->id,
        ]);

        $this->assertDatabaseMissing('daftar_poli', [
            'id_pasien' => $pasienLain->id,
        ]);
    }

    /**
     * TEST 4
     * Pasien tidak boleh memiliki pendaftaran aktif ganda.
     */
    public function test_pendaftaran_ganda_ditolak(): void
    {
        $data = $this->buatDataUji();

        $input = [
            'id_poli' => $data['poli']->id,
            'id_jadwal' => $data['jadwal']->id,
            'keluhan' => 'Demam',
        ];

        // Pendaftaran pertama
        $this->actingAs($data['pasien'])
            ->post(route('pasien.daftar.submit'), $input)
            ->assertRedirect(route('pasien.daftar'));

        // Pendaftaran kedua
        $this->actingAs($data['pasien'])
            ->post(route('pasien.daftar.submit'), $input)
            ->assertSessionHasErrors('id_jadwal');

        // Hanya satu data boleh tersimpan
        $this->assertDatabaseCount('daftar_poli', 1);
    }

    /**
     * TEST 5
     * Nomor antrian harus bertambah secara berurutan.
     */
    public function test_nomor_antrian_berurutan(): void
    {
        $data = $this->buatDataUji();

        $pasienKedua = $this->buatUser('pasien');

        $input = [
            'id_poli' => $data['poli']->id,
            'id_jadwal' => $data['jadwal']->id,
            'keluhan' => 'Sakit kepala',
        ];

        $this->actingAs($data['pasien'])
            ->post(route('pasien.daftar.submit'), $input)
            ->assertRedirect(route('pasien.daftar'));

        $this->actingAs($pasienKedua)
            ->post(route('pasien.daftar.submit'), $input)
            ->assertRedirect(route('pasien.daftar'));

        $this->assertDatabaseHas('daftar_poli', [
            'id_pasien' => $data['pasien']->id,
            'no_antrian' => 1,
        ]);

        $this->assertDatabaseHas('daftar_poli', [
            'id_pasien' => $pasienKedua->id,
            'no_antrian' => 2,
        ]);
    }

    /**
     * TEST 6
     * Pasien boleh mendaftar kembali setelah diperiksa.
     */
    public function test_pasien_boleh_daftar_setelah_pemeriksaan(): void
    {
        $data = $this->buatDataUji();

        $pendaftaran = DaftarPoli::create([
            'id_pasien' => $data['pasien']->id,
            'id_jadwal' => $data['jadwal']->id,
            'keluhan' => 'Keluhan pertama',
            'no_antrian' => 1,
        ]);

        // Pemeriksaan pertama sudah selesai
        Periksa::create([
            'id_daftar_poli' => $pendaftaran->id,
            'tgl_periksa' => now(),
            'catatan' => 'Pemeriksaan selesai',
            'biaya_periksa' => 150000,
        ]);

        // Pasien mencoba mendaftar kembali
        $this->actingAs($data['pasien'])
            ->post(route('pasien.daftar.submit'), [
                'id_poli' => $data['poli']->id,
                'id_jadwal' => $data['jadwal']->id,
                'keluhan' => 'Keluhan kedua',
            ])
            ->assertRedirect(route('pasien.daftar'));

        $this->assertDatabaseCount('daftar_poli', 2);

        $this->assertDatabaseHas('daftar_poli', [
            'id_pasien' => $data['pasien']->id,
            'keluhan' => 'Keluhan kedua',
            'no_antrian' => 2,
        ]);
    }

    /**
     * TEST 7
     * Jadwal yang dipilih harus sesuai dengan poli.
     */
    public function test_jadwal_tidak_sesuai_poli_ditolak(): void
    {
        $data = $this->buatDataUji();

        $poliLain = Poli::create([
            'nama_poli' => 'Poli Gigi Testing',
            'keterangan' => 'Data pengujian',
        ]);

        $this->actingAs($data['pasien'])
            ->post(route('pasien.daftar.submit'), [
                'id_poli' => $poliLain->id,
                'id_jadwal' => $data['jadwal']->id,
                'keluhan' => 'Sakit gigi',
            ])
            ->assertSessionHasErrors('id_jadwal');

        $this->assertDatabaseCount('daftar_poli', 0);
    }

    /**
     * TEST 8
     * Notifikasi sukses hanya ditampilkan satu kali.
     */
    public function test_notifikasi_sukses_tidak_ganda(): void
    {
        $data = $this->buatDataUji();

        $pesan = 'Pesan sukses pengujian regresi';

        $response = $this->actingAs($data['dokter'])
            ->withSession(['success' => $pesan])
            ->get(route('periksa-pasien.index'));

        $response->assertOk();

        $jumlahPesan = substr_count(
            $response->getContent(),
            $pesan
        );

        $this->assertSame(1, $jumlahPesan);
    }
}
