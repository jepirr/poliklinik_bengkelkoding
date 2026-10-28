<?php

namespace Tests\Feature;

use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriksaPasienSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat data dummy khusus pengujian.
     */
    private function dataUji(): array
    {
        $poli = Poli::create([
            'nama_poli' => 'Poli Uji',
            'keterangan' => 'Data untuk automated test',
        ]);

        // Dokter pemilik jadwal
        $dokter = User::create([
            'nama' => 'Dokter A',
            'email' => 'dokter-a@contoh.test',
            'password' => 'password123',
            'role' => 'dokter',
            'id_poli' => $poli->id,
        ]);

        // Dokter lain untuk pengujian keamanan
        $dokterLain = User::create([
            'nama' => 'Dokter B',
            'email' => 'dokter-b@contoh.test',
            'password' => 'password123',
            'role' => 'dokter',
            'id_poli' => $poli->id,
        ]);

        // Pasien testing
        $pasien = User::create([
            'nama' => 'Pasien Uji',
            'email' => 'pasien@contoh.test',
            'password' => 'password123',
            'role' => 'pasien',
        ]);

        // Jadwal milik Dokter A
        $jadwal = JadwalPeriksa::create([
            'id_dokter' => $dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '12:00',
            'status' => 1,
        ]);

        // Pendaftaran pasien
        $pendaftaran = DaftarPoli::create([
            'id_pasien' => $pasien->id,
            'id_jadwal' => $jadwal->id,
            'keluhan' => 'Demam',
            'no_antrian' => 1,
        ]);

        // Obat testing
        $obat = Obat::create([
            'nama_obat' => 'Obat Uji',
            'kemasan' => '1 Strip',
            'harga' => 3000,
        ]);

        return compact(
            'dokter',
            'dokterLain',
            'pendaftaran',
            'obat'
        );
    }

    /**
     * TEST 1:
     * Pemeriksaan yang sama tidak boleh disimpan dua kali.
     */
    public function test_post_pemeriksaan_ganda_ditolak(): void
    {
        $data = $this->dataUji();

        // Simulasikan pasien sudah diperiksa
        Periksa::create([
            'id_daftar_poli' => $data['pendaftaran']->id,
            'tgl_periksa' => now(),
            'catatan' => 'Pemeriksaan pertama',
            'biaya_periksa' => 153000,
        ]);

        // Dokter mencoba menyimpan pemeriksaan kedua
        $response = $this->actingAs($data['dokter'])->post(
            route('periksa-pasien.store'),
            [
                'id_daftar_poli' => $data['pendaftaran']->id,
                'obat_json' => json_encode([$data['obat']->id]),
                'catatan' => 'Pemeriksaan kedua',
                'biaya_periksa' => 1,
            ]
        );

        // Harus ditolak oleh validasi controller
        $response->assertSessionHasErrors('obat_json');

        // Database hanya boleh berisi satu pemeriksaan
        $this->assertDatabaseCount('periksa', 1);

        $this->assertDatabaseMissing('periksa', [
            'catatan' => 'Pemeriksaan kedua',
        ]);
    }

    /**
     * TEST 2:
     * Dokter B tidak boleh memeriksa pasien Dokter A.
     */
    public function test_dokter_lain_tidak_bisa_memeriksa_pasien(): void
    {
        $data = $this->dataUji();

        // Dokter B mencoba membuka form
        $this->actingAs($data['dokterLain'])
            ->get(route(
                'periksa-pasien.create',
                $data['pendaftaran']->id
            ))
            ->assertNotFound();

        // Dokter B mencoba menyimpan pemeriksaan
        $this->actingAs($data['dokterLain'])
            ->post(route('periksa-pasien.store'), [
                'id_daftar_poli' => $data['pendaftaran']->id,
                'obat_json' => json_encode([$data['obat']->id]),
                'catatan' => 'Tidak berwenang',
                'biaya_periksa' => 1,
            ])
            ->assertNotFound();

        // Tidak boleh ada pemeriksaan tersimpan
        $this->assertDatabaseCount('periksa', 0);
    }

    /**
     * TEST 3:
     * Total biaya harus dihitung dari database.
     */
    public function test_biaya_dihitung_dari_database_bukan_input_browser(): void
    {
        $data = $this->dataUji();

        // Browser mengirim biaya palsu sebesar Rp1
        $this->actingAs($data['dokter'])
            ->post(route('periksa-pasien.store'), [
                'id_daftar_poli' => $data['pendaftaran']->id,
                'obat_json' => json_encode([$data['obat']->id]),
                'catatan' => 'Pemeriksaan berhasil',
                'biaya_periksa' => 1,
            ])
            ->assertRedirect(route('periksa-pasien.index'));

        // Biaya asli tetap Rp150.000 + Rp3.000
        $this->assertDatabaseHas('periksa', [
            'id_daftar_poli' => $data['pendaftaran']->id,
            'biaya_periksa' => 153000,
        ]);

        // Obat tetap tersimpan
        $this->assertDatabaseHas('detail_periksa', [
            'id_obat' => $data['obat']->id,
        ]);
    }
}
