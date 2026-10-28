<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\Periksa;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RiwayatPendaftaranController extends Controller
{
    /**
     * Menampilkan seluruh riwayat pendaftaran
     * milik pasien yang sedang login.
     */
    public function index(): View
    {
        $riwayatPendaftaran = DaftarPoli::query()
            ->where('id_pasien', Auth::id())
            ->with([
                'jadwalPeriksa.dokter.poli',
            ])
            ->withExists('periksas')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view(
            'pasien.riwayat-pendaftaran.index',
            compact('riwayatPendaftaran')
        );
    }

    /**
     * Menampilkan detail pemeriksaan.
     *
     * ID yang diterima adalah ID daftar_poli,
     * bukan ID pemeriksaan.
     */
    public function show(int $id): View
    {
        // Hanya mengambil pendaftaran milik pasien login.
        $pendaftaran = DaftarPoli::query()
            ->where('id_pasien', Auth::id())
            ->with('jadwalPeriksa.dokter.poli')
            ->findOrFail($id);

        // Detail hanya tersedia jika sudah diperiksa.
        $periksa = Periksa::query()
            ->where('id_daftar_poli', $pendaftaran->id)
            ->with('detailPeriksas.obat')
            ->orderByDesc('tgl_periksa')
            ->firstOrFail();

        return view(
            'pasien.riwayat-pendaftaran.show',
            compact('pendaftaran', 'periksa')
        );
    }
}
