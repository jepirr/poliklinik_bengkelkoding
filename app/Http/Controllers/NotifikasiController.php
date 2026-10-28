<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function index(Request $request): View
    {
        $notifikasis = $request->user()
            ->notifications()
            ->orderByDesc('created_at')
            ->paginate(10);

        $jumlahBelumDibaca = $request->user()
            ->unreadNotifications()
            ->count();

        return view('notifikasi.index', compact(
            'notifikasis',
            'jumlahBelumDibaca'
        ));
    }

    public function baca(
        Request $request,
        string $id
    ): RedirectResponse {
        $notifikasi = $request->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();

        if ($notifikasi->read_at === null) {
            $notifikasi->markAsRead();
        }

        return redirect()
            ->route('notifikasi.index')
            ->with(
                'success',
                'Notifikasi ditandai sebagai sudah dibaca.'
            );
    }
}
