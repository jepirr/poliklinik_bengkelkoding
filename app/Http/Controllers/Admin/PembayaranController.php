<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Notifications\PembayaranLunas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(): View
    {
        $pembayarans = Pembayaran::query()
            ->with([
                'periksa.daftarPoli.pasien',
                'periksa.daftarPoli.jadwalPeriksa.dokter.poli',
            ])
            ->where('status', 'menunggu_verifikasi')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view(
            'admin.pembayaran.index',
            compact('pembayarans')
        );
    }

    public function bukti(int $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        abort_unless(
            Storage::disk('local')->exists(
                $pembayaran->bukti_pembayaran
            ),
            404,
            'Bukti pembayaran tidak ditemukan.'
        );

        return Storage::disk('local')->response(
            $pembayaran->bukti_pembayaran
        );
    }

    public function verifikasi(int $id): RedirectResponse
    {
        $berhasil = DB::transaction(function () use ($id) {
            // Lock mencegah dua permintaan verifikasi
            // memproses pembayaran yang sama.
            $pembayaran = Pembayaran::query()
                ->with('periksa.daftarPoli.pasien')
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            // Jika sudah lunas, tidak diproses lagi.
            if (
                $pembayaran->status
                !== 'menunggu_verifikasi'
            ) {
                return false;
            }

            $periksa = $pembayaran->periksa;
            $pasien = $periksa?->daftarPoli?->pasien;

            abort_unless(
                $pasien && $pasien->role === 'pasien',
                422,
                'Akun pasien tidak ditemukan.'
            );

            // Status pembayaran berubah menjadi lunas.
            $pembayaran->update([
                'status' => 'lunas',
            ]);

            // NOTIFIKASI 2:
            // Dikirim hanya pada perubahan status
            // menunggu_verifikasi menjadi lunas.
            $pasien->notify(
                new PembayaranLunas(
                    (int) $pembayaran->id,
                    (int) $periksa->biaya_periksa
                )
            );

            return true;
        });

        if (!$berhasil) {
            return redirect()
                ->route('admin.pembayaran.index')
                ->with(
                    'error',
                    'Pembayaran tersebut sudah diverifikasi.'
                );
        }

        return redirect()
            ->route('admin.pembayaran.index')
            ->with(
                'success',
                'Pembayaran berhasil diverifikasi dan dinyatakan lunas.'
            );
    }
}
