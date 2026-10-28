<?php

namespace App\Http\Controllers\Dokter;

use App\Events\AntrianDipanggil;
use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PanggilAntrianController extends Controller
{
    public function __invoke(int $id): RedirectResponse
    {
        // Periksa kepemilikan dokter dan simpan nomor
        // dengan transaksi untuk menghindari perubahan bersamaan.
        $hasil = DB::transaction(function () use ($id) {

            $pendaftaran = DaftarPoli::query()
                ->whereKey($id)
                ->whereHas('jadwalPeriksa', function ($query) {
                    $query->where('id_dokter', Auth::id());
                })
                ->firstOrFail();

            $jadwal = JadwalPeriksa::query()
                ->whereKey($pendaftaran->id_jadwal)
                ->where('id_dokter', Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                (bool) $jadwal->status,
                422,
                'Jadwal pemeriksaan tidak aktif.'
            );

            abort_if(
                $pendaftaran->periksas()->exists(),
                409,
                'Pasien sudah diperiksa.'
            );

            // Catat nomor yang sedang dipanggil.
            $jadwal->no_antrian_dilayani =
                (int) $pendaftaran->no_antrian;

            $jadwal->save();

            return [
                'jadwal_id' => (int) $jadwal->id,
                'nomor' => (int) $pendaftaran->no_antrian,
            ];
        });

        // Broadcast setelah transaksi berhasil disimpan.
        event(new AntrianDipanggil(
            $hasil['jadwal_id'],
            $hasil['nomor']
        ));

        return redirect()
            ->route('periksa-pasien.index')
            ->with(
                'success',
                'Antrean nomor ' . $hasil['nomor'] .
                ' berhasil dipanggil.'
            );
    }
}
