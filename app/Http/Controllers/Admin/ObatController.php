<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObatController extends Controller
{
    public function index(): View
    {
        $obats = Obat::orderByDesc('id')->paginate(10);

        return view('admin.obat.index', compact('obats'));
    }

    public function create(): View
    {
        return view('admin.obat.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'kemasan' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0|max:2147483647',
        ], [
            'nama_obat.required' => 'Nama obat wajib diisi.',
            'kemasan.required' => 'Kemasan wajib diisi.',
            'stok.required' => 'Stok obat wajib diisi.',
            'stok.integer' => 'Stok harus berupa bilangan bulat.',
            'stok.min' => 'Stok tidak boleh negatif.',
            'harga.required' => 'Harga obat wajib diisi.',
            'harga.integer' => 'Harga harus berupa bilangan bulat.',
            'harga.min' => 'Harga tidak boleh negatif.',
        ]);

        Obat::create($validated);

        return redirect()
            ->route('obat.index')
            ->with('success', 'Data obat berhasil disimpan.');
    }

    public function edit(Obat $obat): View
    {
        return view('admin.obat.edit', compact('obat'));
    }

    public function update(
        Request $request,
        Obat $obat
    ): RedirectResponse {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'kemasan' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0|max:2147483647',
        ], [
            'nama_obat.required' => 'Nama obat wajib diisi.',
            'kemasan.required' => 'Kemasan wajib diisi.',
            'stok.required' => 'Stok obat wajib diisi.',
            'stok.integer' => 'Stok harus berupa bilangan bulat.',
            'stok.min' => 'Stok tidak boleh negatif.',
            'harga.required' => 'Harga obat wajib diisi.',
            'harga.integer' => 'Harga harus berupa bilangan bulat.',
            'harga.min' => 'Harga tidak boleh negatif.',
        ]);

        $obat->update($validated);

        return redirect()
            ->route('obat.index')
            ->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Obat $obat): RedirectResponse
    {
        try {
            $obat->delete();

            return redirect()
                ->route('obat.index')
                ->with('success', 'Data obat berhasil dihapus.');

        } catch (QueryException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }

            return redirect()
                ->route('obat.index')
                ->with(
                    'error',
                    'Obat tidak dapat dihapus karena sedang digunakan.'
                );
        }
    }
}
