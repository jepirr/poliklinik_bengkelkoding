<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna login.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('profil.index', compact('user'));
    }

    /**
     * Memproses perubahan password pengguna login.
     */
    public function updatePassword(
        Request $request
    ): RedirectResponse {

        // Validasi semua input password.
        $validated = $request->validate(
            [
                'current_password' => [
                    'bail',
                    'required',
                    'current_password',
                ],

                'new_password' => [
                    'bail',
                    'required',
                    'string',
                    'min:8',
                    'different:current_password',
                    'confirmed',
                ],

                'new_password_confirmation' => [
                    'required',
                    'string',
                ],
            ],
            [
                'current_password.required' =>
                    'Password lama wajib diisi.',

                'current_password.current_password' =>
                    'Password lama tidak sesuai.',

                'new_password.required' =>
                    'Password baru wajib diisi.',

                'new_password.min' =>
                    'Password baru minimal 8 karakter.',

                'new_password.different' =>
                    'Password baru harus berbeda dari password lama.',

                'new_password.confirmed' =>
                    'Konfirmasi password baru tidak sesuai.',

                'new_password_confirmation.required' =>
                    'Konfirmasi password baru wajib diisi.',
            ]
        );

        // Ambil pengguna yang sedang login.
        // Tidak menerima ID pengguna dari request.
        $user = $request->user();

        // Pemeriksaan tambahan untuk memastikan
        // password baru tidak sama dengan hash lama.
        if (
            Hash::check(
                $validated['new_password'],
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'new_password' =>
                    'Password baru tidak boleh sama dengan password lama.',
            ]);
        }

        // Simpan password dalam bentuk hash.
        $user->password = Hash::make(
            $validated['new_password']
        );

        $user->save();

        return redirect()
            ->route('profil.index')
            ->with(
                'success',
                'Password berhasil diubah.'
            );
    }
}
