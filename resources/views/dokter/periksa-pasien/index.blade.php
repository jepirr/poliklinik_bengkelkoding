
<x-layouts.app title="Periksa Pasien">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Periksa Pasien
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Kelola daftar antrian pasien yang akan diperiksa.
            </p>
        </div>
    </div>

    {{-- ================= CARD TABLE ================= --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- TABLE HEAD --}}
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Nama Pasien</th>
                            <th class="px-6 py-4">Keluhan</th>
                            <th class="px-6 py-4">No. Antrian</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- TABLE BODY --}}
                    <tbody class="text-sm text-slate-700">

                        @forelse($daftarPasien as $pasien)

                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">

                                {{-- ID --}}
                                <td class="px-6 py-4">
                                    {{ $pasien->id }}
                                </td>

                                {{-- NAMA PASIEN --}}
                                <td class="px-6 py-4 font-semibold text-slate-800">
                                    {{ $pasien->pasien?->nama ?? '-' }}
                                </td>

                                {{-- KELUHAN --}}
                                <td class="px-6 py-4">
                                    {{ $pasien->keluhan ?? '-' }}
                                </td>

                                {{-- NOMOR ANTRIAN --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center justify-center
                                                 min-w-9 h-9 rounded-lg
                                                 bg-blue-100 text-blue-700
                                                 font-bold text-sm">
                                        {{ $pasien->no_antrian }}
                                    </span>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-6 py-4 text-right">
   
                                @if($pasien->periksas->isNotEmpty())

                                    {{-- PASIEN SUDAH DIPERIKSA --}}
                                    <span class="inline-flex items-center gap-2 px-3 py-2
                                                rounded-lg bg-green-100 text-green-700
                                                text-xs font-semibold">

                                        <i class="fas fa-circle-check"></i>
                                        Sudah Diperiksa

                                    </span>

                                @else

                                    <div class="flex flex-wrap items-center justify-end gap-2">

                                        {{-- NOMOR YANG SEDANG DIPANGGIL --}}
                                        @if(
                                            !is_null($pasien->jadwalPeriksa?->no_antrian_dilayani)
                                            &&
                                            (int) $pasien->jadwalPeriksa->no_antrian_dilayani
                                                === (int) $pasien->no_antrian
                                        )

                                            <span class="badge badge-success text-white
                                                        font-semibold">
                                                Sedang Dipanggil
                                            </span>

                                        @endif

                                        {{-- TOMBOL PANGGIL --}}
                                        <form method="POST"
                                            action="{{ route('periksa-pasien.panggil', $pasien->id) }}">

                                            @csrf

                                            <button type="submit"
                                                    @disabled(!$pasien->jadwalPeriksa?->status)
                                                    class="inline-flex items-center gap-2
                                                        px-4 py-2 rounded-lg
                                                        bg-indigo-600 hover:bg-indigo-700
                                                        text-white text-xs font-semibold
                                                        transition disabled:opacity-50
                                                        disabled:cursor-not-allowed">

                                                <i class="fas fa-bullhorn"></i>
                                                Panggil

                                            </button>

                                        </form>

                                        {{-- TOMBOL PERIKSA YANG SUDAH ADA --}}
                                        <a href="{{ route('periksa-pasien.create', $pasien->id) }}"
                                        class="inline-flex items-center gap-2
                                                px-4 py-2 rounded-lg
                                                bg-amber-500 hover:bg-amber-600
                                                text-white text-xs font-semibold
                                                transition">

                                            <i class="fas fa-stethoscope"></i>
                                            Periksa

                                        </a>

                                    </div>

                                @endif


                                </td>

                            </tr>

                        @empty

                            {{-- DATA KOSONG --}}
                            <tr>
                                <td colspan="5"
                                    class="text-center py-16 text-slate-400">

                                    <i class="fas fa-user-doctor text-3xl mb-3 block"></i>

                                    Belum ada pasien dalam antrian pemeriksaan.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>
    </div>

</x-layouts.app>
