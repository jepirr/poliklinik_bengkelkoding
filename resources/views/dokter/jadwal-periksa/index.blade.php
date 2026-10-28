<x-layouts.app title="Jadwal Periksa">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Jadwal Periksa
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Kelola jadwal praktik dan pemeriksaan dokter.
            </p>
        </div>


        <a href="{{ route('export.dokter.jadwal') }}" class="inline-flex items-center justify-center
          gap-2 px-5 py-2.5 bg-emerald-600
          hover:bg-emerald-700 text-white
          rounded-xl text-sm font-semibold transition ml-auto">

            <i class="fas fa-file-excel"></i>
            Export Excel

        </a>


        <a href="{{ route('jadwal-periksa.create') }}" class="inline-flex items-center justify-center gap-2
                  px-5 py-2.5 bg-primary hover:bg-primary/90
                  text-white rounded-xl text-sm font-semibold transition">
            <i class="fas fa-plus text-sm"></i>
            Tambah Jadwal
        </a>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div role="alert" class="alert alert-success mb-5 rounded-xl text-white">
            <i class="fas fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div role="alert" class="alert alert-error mb-5 rounded-xl text-white">
            <i class="fas fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Card Table --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    {{-- Table Head --}}
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Hari</th>
                            <th class="px-6 py-4">Jam Mulai</th>
                            <th class="px-6 py-4">Jam Selesai</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Table Body --}}
                    <tbody class="text-sm text-slate-700">
                        @forelse($jadwals as $jadwal)
                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition">

                                <td class="px-6 py-4">
                                    {{ $jadwals->firstItem() + $loop->index }}
                                </td>

                                <td class="px-6 py-4 font-semibold text-slate-800">
                                    {{ $jadwal->hari }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-2">
                                        <i class="far fa-clock text-slate-400"></i>
                                        {{ substr($jadwal->jam_mulai, 0, 5) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-2">
                                        <i class="far fa-clock text-slate-400"></i>
                                        {{ substr($jadwal->jam_selesai, 0, 5) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    @if($jadwal->status)
                                        <span class="inline-flex items-center gap-1
                                                             px-3 py-1 rounded-full text-xs
                                                             font-semibold bg-green-100 text-green-700">
                                            <i class="fas fa-circle-check"></i>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1
                                                             px-3 py-1 rounded-full text-xs
                                                             font-semibold bg-slate-100 text-slate-600">
                                            <i class="fas fa-circle-minus"></i>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">

                                        {{-- Edit --}}
                                        <a href="{{ route('jadwal-periksa.edit', $jadwal->id) }}" class="inline-flex items-center gap-1 px-3 py-2
                                                      bg-amber-500 hover:bg-amber-600
                                                      text-white rounded-lg text-xs
                                                      font-semibold transition">
                                            <i class="fas fa-pen-to-square text-xs"></i>
                                            Edit
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('jadwal-periksa.destroy', $jadwal->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-2
                                                               bg-red-500 hover:bg-red-600
                                                               text-white rounded-lg text-xs
                                                               font-semibold transition">
                                                <i class="fas fa-trash text-xs"></i>
                                                Hapus
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-16 text-slate-400">
                                    <i class="fas fa-calendar-xmark text-3xl mb-3 block"></i>
                                    Belum ada jadwal periksa
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- Pagination --}}
            @if($jadwals->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $jadwals->links() }}
                </div>
            @endif

        </div>
    </div>

</x-layouts.app>