<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class JadwalPeriksaController extends Controller
{
    public function index(): View
    {
        $dokter = Auth::user();

        $jadwals = JadwalPeriksa::where(
            'id_dokter',
            $dokter->getAuthIdentifier()
        )
            ->orderByDesc('id')
            ->paginate(10);

        return view(
            'dokter.jadwal-periksa.index',
            compact('jadwals')
        );
    }

    public function create(): View
    {
        return view('dokter.jadwal-periksa.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->rules(),
            $this->messages()
        );

        $dokter = Auth::user();

        // ID dokter tidak boleh berasal dari input form.
        $validated['id_dokter'] = $dokter->getAuthIdentifier();

        JadwalPeriksa::create($validated);

        return redirect()
            ->route('jadwal-periksa.index')
            ->with('success', 'Data Berhasil disimpan');
    }

    public function edit(JadwalPeriksa $jadwalPeriksa): View
    {
        $this->authorizeOwner($jadwalPeriksa);

        return view(
            'dokter.jadwal-periksa.edit',
            compact('jadwalPeriksa')
        );
    }

    public function update(
        Request $request,
        JadwalPeriksa $jadwalPeriksa
    ): RedirectResponse {
        $this->authorizeOwner($jadwalPeriksa);

        $validated = $request->validate(
            $this->rules(),
            $this->messages()
        );

        $jadwalPeriksa->update($validated);

        return redirect()
            ->route('jadwal-periksa.index')
            ->with('success', 'Data Berhasil diperbarui');
    }

    public function destroy(
        JadwalPeriksa $jadwalPeriksa
    ): RedirectResponse {
        $this->authorizeOwner($jadwalPeriksa);

        try {
            $jadwalPeriksa->delete();

            return redirect()
                ->route('jadwal-periksa.index')
                ->with('success', 'Data Berhasil dihapus');

        } catch (QueryException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }

            return redirect()
                ->route('jadwal-periksa.index')
                ->with(
                    'error',
                    'Jadwal tidak dapat dihapus karena sedang digunakan.'
                );
        }
    }

    // Validasi untuk tambah dan edit jadwal
    private function rules(): array
    {
        return [
            'hari' => [
                'required',
                Rule::in([
                    'Senin',
                    'Selasa',
                    'Rabu',
                    'Kamis',
                    'Jumat',
                    'Sabtu',
                    'Minggu',
                ]),
            ],

            'jam_mulai' => [
                'required',
                'date_format:H:i',
            ],

            'jam_selesai' => [
                'required',
                'date_format:H:i',
                'after:jam_mulai',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ];
    }

    // Pesan error validasi
    private function messages(): array
    {
        return [
            'hari.required' => 'Hari wajib dipilih.',
            'hari.in' => 'Hari yang dipilih tidak valid.',

            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid.',

            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid.',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',

            'status.required' => 'Status wajib dipilih.',
            'status.boolean' => 'Status yang dipilih tidak valid.',
        ];
    }

    // Cegah dokter mengubah jadwal dokter lain
    private function authorizeOwner(
        JadwalPeriksa $jadwalPeriksa
    ): void {
        $dokter = Auth::user();

        abort_unless(
            (string) $jadwalPeriksa->id_dokter ===
            (string) $dokter->getAuthIdentifier(),
            403,
            'Anda tidak memiliki akses ke jadwal ini.'
        );
    }
}
