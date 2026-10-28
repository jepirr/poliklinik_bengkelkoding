<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PoliController extends Controller
{
    /**
     * Menampilkan form pendaftaran poli.
     */
    public function get(): View
    {
        $user = Auth::user();

        // Ambil jadwal aktif yang mempunyai dokter dan poli.
        $jadwals = JadwalPeriksa::with('dokter.poli')
            ->where('status', true)
            ->whereHas('dokter', function ($query) {
                $query->where('role', 'dokter')
                    ->whereHas('poli');
            })
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Hanya tampilkan poli yang memiliki jadwal aktif.
        $poliIds = $jadwals
            ->pluck('dokter.id_poli')
            ->filter()
            ->unique()
            ->values();

        $polis = Poli::whereIn('id', $poliIds)
            ->orderBy('nama_poli')
            ->get();

        // Pendaftaran yang belum memiliki hasil pemeriksaan.
        $pendaftaranAktif = DaftarPoli::with(
            'jadwalPeriksa.dokter.poli'
        )
            ->where('id_pasien', $user->id)
            ->whereDoesntHave('periksas')
            ->latest('id')
            ->first();

        return view('pasien.daftar', compact(
            'user',
            'polis',
            'jadwals',
            'pendaftaranAktif'
        ));
    }

    /**
     * Menyimpan pendaftaran poli pasien.
     */
    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'id_poli' => [
                    'required',
                    'integer',
                    Rule::exists('poli', 'id'),
                ],
                'id_jadwal' => [
                    'required',
                    'integer',
                    Rule::exists('jadwal_periksa', 'id'),
                ],
                'keluhan' => [
                    'required',
                    'string',
                    'max:2000',
                ],
            ],
            [
                'id_poli.required' => 'Poli wajib dipilih.',
                'id_poli.exists' => 'Poli tidak ditemukan.',
                'id_jadwal.required' => 'Jadwal wajib dipilih.',
                'id_jadwal.exists' => 'Jadwal tidak ditemukan.',
                'keluhan.required' => 'Keluhan wajib diisi.',
                'keluhan.max' => 'Keluhan maksimal 2000 karakter.',
            ]
        );

        DB::transaction(function () use ($validated) {

            // Kunci akun pasien selama proses pendaftaran.
            $pasien = User::whereKey(Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($pasien->role === 'pasien', 403);

            // Cek pendaftaran yang belum selesai diperiksa.
            $sudahMendaftar = DaftarPoli::where(
                'id_pasien',
                $pasien->id
            )
                ->whereDoesntHave('periksas')
                ->exists();

            if ($sudahMendaftar) {
                throw ValidationException::withMessages([
                    'id_jadwal' =>
                        'Anda masih memiliki antrian aktif. ' .
                        'Selesaikan pemeriksaan terlebih dahulu.',
                ]);
            }

            // Ambil dan kunci jadwal tujuan.
            $jadwal = JadwalPeriksa::with('dokter')
                ->whereKey($validated['id_jadwal'])
                ->lockForUpdate()
                ->first();

            // Pastikan jadwal aktif dan sesuai poli.
            if (
                !$jadwal ||
                !$jadwal->status ||
                !$jadwal->dokter ||
                $jadwal->dokter->role !== 'dokter' ||
                (int) $jadwal->dokter->id_poli !==
                (int) $validated['id_poli']
            ) {
                throw ValidationException::withMessages([
                    'id_jadwal' =>
                        'Jadwal tidak aktif atau tidak sesuai dengan poli.',
                ]);
            }

            // Tentukan nomor antrian berikutnya.
            $nomorTerakhir = DaftarPoli::where(
                'id_jadwal',
                $jadwal->id
            )->max('no_antrian');

            $nomorAntrian = ((int) $nomorTerakhir) + 1;

            // Simpan menggunakan ID pasien yang login.
            DaftarPoli::create([
                'id_pasien' => $pasien->id,
                'id_jadwal' => $jadwal->id,
                'keluhan' => $validated['keluhan'],
                'no_antrian' => $nomorAntrian,
            ]);
        });

        return redirect()
            ->route('pasien.daftar')
            ->with(
                'success',
                'Pendaftaran poli berhasil disimpan.'
            );
    }
}
