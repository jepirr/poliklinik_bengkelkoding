<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Events\AntrianDipanggil;
use App\Models\JadwalPeriksa;
use Illuminate\View\View;

class PeriksaPasienController extends Controller
{
    // Biaya dasar pemeriksaan
    private const BIAYA_DASAR = 150000;

    /**
     * Menampilkan antrian pasien milik dokter login.
     */
    public function index(): View
    {
        $daftarPasien = DaftarPoli::with([
            'pasien',
            'jadwalPeriksa',
            'periksas',
        ])
            ->whereHas('jadwalPeriksa', function ($query) {
                $query->where('id_dokter', Auth::id());
            })
            ->orderBy('no_antrian')
            ->get();

        return view(
            'dokter.periksa-pasien.index',
            compact('daftarPasien')
        );
    }

    /**
     * Menampilkan form pemeriksaan.
     */
    public function create(int $id): View
    {
        $pendaftaran = DaftarPoli::whereHas(
            'jadwalPeriksa',
            function ($query) {
                $query->where('id_dokter', Auth::id());
            }
        )->findOrFail($id);

        // Cegah pemeriksaan ulang
        abort_if(
            $pendaftaran->periksas()->exists(),
            409,
            'Pasien ini sudah diperiksa.'
        );

        $obats = Obat::orderBy('nama_obat')->get();

        return view(
            'dokter.periksa-pasien.create',
            compact('obats', 'id')
        );
    }

    /**
     * Menyimpan hasil pemeriksaan pasien.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input utama
        $data = $request->validate(
            [
                'id_daftar_poli' => [
                    'required',
                    'integer',
                    'exists:daftar_poli,id',
                ],
                'obat_json' => [
                    'required',
                    'json',
                ],
                'catatan' => [
                    'nullable',
                    'string',
                ],
            ],
            [
                'id_daftar_poli.required' =>
                    'Pendaftaran pasien tidak ditemukan.',
                'id_daftar_poli.exists' =>
                    'Pendaftaran pasien tidak valid.',
                'obat_json.required' =>
                    'Pilih minimal satu obat.',
                'obat_json.json' =>
                    'Data obat tidak valid.',
            ]
        );

        // Ubah JSON menjadi array ID obat
        $obatIds = json_decode($data['obat_json'], true);

        if (!is_array($obatIds) || !array_is_list($obatIds)) {
            throw ValidationException::withMessages([
                'obat_json' => 'Format daftar obat tidak valid.',
            ]);
        }

        // Validasi ID obat
        $validator = Validator::make(
            ['obat_ids' => $obatIds],
            [
                'obat_ids' => [
                    'required',
                    'array',
                    'min:1',
                ],
                'obat_ids.*' => [
                    'required',
                    'integer',
                    'distinct',
                    Rule::exists('obat', 'id'),
                ],
            ],
            [
                'obat_ids.min' =>
                    'Pilih minimal satu obat.',
                'obat_ids.*.integer' =>
                    'ID obat tidak valid.',
                'obat_ids.*.distinct' =>
                    'Obat tidak boleh dipilih dua kali.',
                'obat_ids.*.exists' =>
                    'Terdapat obat yang tidak tersedia.',
            ]
        );

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'obat_json' => $validator->errors()->first(),
            ]);
        }

        // Semua penyimpanan dilakukan dalam transaksi
        $jadwalYangSelesai = DB::transaction(function () use ($data, $obatIds) {

            // Pastikan pendaftaran milik dokter login
            $pendaftaran = DaftarPoli::whereHas(
                'jadwalPeriksa',
                function ($query) {
                    $query->where('id_dokter', Auth::id());
                }
            )
                ->whereKey($data['id_daftar_poli'])
                ->lockForUpdate()
                ->firstOrFail();

            // Pastikan belum pernah diperiksa
            if ($pendaftaran->periksas()->exists()) {
                throw ValidationException::withMessages([
                    'obat_json' =>
                        'Pasien ini sudah diperiksa.',
                ]);
            }

            // Ambil harga obat langsung dari database
            $obats = Obat::whereIn('id', $obatIds)
                ->get(['id', 'harga']);

            if ($obats->count() !== count($obatIds)) {
                throw ValidationException::withMessages([
                    'obat_json' =>
                        'Daftar obat berubah. Silakan pilih ulang.',
                ]);
            }

            // Hitung biaya pemeriksaan
            $totalBiaya = self::BIAYA_DASAR
                + (int) $obats->sum('harga');

            // Kolom biaya_periksa menggunakan INTEGER
            if ($totalBiaya > 2147483647) {
                throw ValidationException::withMessages([
                    'obat_json' =>
                        'Total biaya melebihi batas penyimpanan.',
                ]);
            }

            // Simpan data pemeriksaan
            $periksa = Periksa::create([
                'id_daftar_poli' => $pendaftaran->id,
                'tgl_periksa' => now(),
                'catatan' => $data['catatan'] ?? null,
                'biaya_periksa' => $totalBiaya,
            ]);

            // Simpan detail obat
            foreach ($obatIds as $idObat) {
                $periksa->detailPeriksas()->create([
                    'id_obat' => $idObat,
                ]);
            }


            // Jika pasien yang diperiksa adalah antrean
            // yang sedang dilayani, kosongkan nomor tersebut.
            $direset = JadwalPeriksa::query()
                ->whereKey($pendaftaran->id_jadwal)
                ->where(
                    'no_antrian_dilayani',
                    $pendaftaran->no_antrian
                )
                ->update([
                    'no_antrian_dilayani' => null,
                ]);

            return $direset > 0
                ? (int) $pendaftaran->id_jadwal
                : null;

        });

        if ($jadwalYangSelesai !== null) {
            event(new AntrianDipanggil(
                $jadwalYangSelesai,
                null
            ));
        }

        return redirect()
            ->route('periksa-pasien.index')
            ->with(
                'success',
                'Data periksa berhasil disimpan.'
            );
    }
}
