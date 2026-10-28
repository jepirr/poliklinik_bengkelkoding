<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Pendaftaran aktif milik pasien login.
        // Sudah diperiksa berarti tidak lagi aktif.
        $antreanAktif = DaftarPoli::query()
            ->with('jadwalPeriksa.dokter.poli')
            ->where('id_pasien', Auth::id())
            ->whereDoesntHave('periksas')
            ->latest('id')
            ->first();

        // Semua jadwal beserta dokter dan polinya.
        // Eager loading mencegah N+1 query.
        $jadwals = JadwalPeriksa::query()
            ->with('dokter.poli')
            ->orderBy('id')
            ->get();

        return view('pasien.dashboard', compact(
            'antreanAktif',
            'jadwals'
        ));
    }
}
