<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Periksa;
use App\Notifications\BuktiPembayaranDiunggah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(): View
    {
        $tagihans = Periksa::query()
            ->whereHas('daftarPoli', function ($query) {
                $query->where('id_pasien', Auth::id());
            })
            ->with([
                'daftarPoli.jadwalPeriksa.dokter.poli',
                'pembayaran',
            ])
            ->orderByDesc('tgl_periksa')
            ->paginate(10);

        return view(
            'pasien.pembayaran.index',
            compact('tagihans')
        );
    }

    public function store(
        Request $request,
        int $id
    ): RedirectResponse {
        // Pastikan tagihan milik pasien yang login.
        $tagihan = Periksa::query()
            ->whereHas('daftarPoli', function ($query) {
                $query->where('id_pasien', Auth::id());
            })
            ->findOrFail($id);

        $request->validate([
            'bukti_pembayaran' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ], [
            'bukti_pembayaran.required' =>
                'Foto bukti pembayaran wajib diunggah.',
            'bukti_pembayaran.image' =>
                'File harus berupa gambar.',
            'bukti_pembayaran.mimes' =>
                'Format gambar harus JPG, PNG, atau WEBP.',
            'bukti_pembayaran.max' =>
                'Ukuran gambar maksimal 4 MB.',
        ]);

        $path = null;

        try {
            DB::transaction(function () use (
                $tagihan,
                $request,
                &$path
            ) {
                // Kunci pemeriksaan agar upload ganda
                // tidak menghasilkan dua pembayaran.
                $periksa = Periksa::query()
                    ->whereKey($tagihan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($periksa->pembayaran()->exists()) {
                    throw ValidationException::withMessages([
                        'bukti_pembayaran' =>
                            'Bukti pembayaran untuk tagihan ini sudah diunggah.',
                    ]);
                }

                // Ambil dokter dari jadwal pemeriksaan.
                $pendaftaran = $periksa->daftarPoli()
                    ->with([
                        'pasien',
                        'jadwalPeriksa.dokter',
                    ])
                    ->firstOrFail();

                $dokter = $pendaftaran
                    ->jadwalPeriksa?->dokter;

                if (!$dokter || $dokter->role !== 'dokter') {
                    throw ValidationException::withMessages([
                        'bukti_pembayaran' =>
                            'Dokter pemeriksa tidak ditemukan.',
                    ]);
                }

                // Simpan foto bukti di storage private.
                $path = $request
                    ->file('bukti_pembayaran')
                    ->store('bukti-pembayaran', 'local');

                if (!$path) {
                    throw ValidationException::withMessages([
                        'bukti_pembayaran' =>
                            'Gagal menyimpan foto pembayaran.',
                    ]);
                }

                // Simpan pembayaran.
                $pembayaran = Pembayaran::create([
                    'id_periksa' => $periksa->id,
                    'bukti_pembayaran' => $path,
                    'status' => 'menunggu_verifikasi',
                ]);

                // NOTIFIKASI 1:
                // Kirim sekali kepada dokter pemeriksa.
                $dokter->notify(
                    new BuktiPembayaranDiunggah(
                        (int) $pembayaran->id,
                        $pendaftaran->pasien?->nama
                            ?? 'Pasien'
                    )
                );
            });
        } catch (\Throwable $e) {
            // Jika transaksi gagal, hapus file
            // yang sempat disimpan.
            if ($path !== null) {
                Storage::disk('local')->delete($path);
            }

            throw $e;
        }

        return redirect()
            ->route('pasien.pembayaran.index')
            ->with(
                'success',
                'Bukti pembayaran berhasil diunggah dan menunggu verifikasi admin.'
            );
    }
}
