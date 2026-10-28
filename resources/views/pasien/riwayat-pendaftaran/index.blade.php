
<x-layouts.app title="Riwayat Pendaftaran Poli">

    {{-- HEADER --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Riwayat Pendaftaran Poli
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Daftar seluruh pendaftaran pemeriksaan
            yang pernah Anda lakukan.
        </p>
    </div>

    {{-- TABEL RIWAYAT --}}
    <div class="card bg-base-100 rounded-2xl
                border border-slate-200 shadow-md">

        <div class="card-body p-0">

            <div class="overflow-x-auto">

                <table class="table w-full">

                    <thead class="bg-slate-100
                                 text-slate-600 text-xs uppercase">

                        <tr>
                            <th class="px-5 py-4">No.</th>
                            <th class="px-5 py-4">Tanggal Pendaftaran</th>
                            <th class="px-5 py-4">Poli</th>
                            <th class="px-5 py-4">Dokter</th>
                            <th class="px-5 py-4">Jadwal</th>
                            <th class="px-5 py-4 text-center">Antrian</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>

                    </thead>

                    <tbody class="text-sm text-slate-700">

                        @forelse($riwayatPendaftaran as $riwayat)

                            @php
                                $jadwal = $riwayat->jadwalPeriksa;
                                $dokter = $jadwal?->dokter;
                                $sudahDiperiksa = (bool) $riwayat->periksas_exists;
                            @endphp

                            <tr class="border-b border-slate-100
                                       hover:bg-slate-50 transition">

                                {{-- NOMOR --}}
                                <td class="px-5 py-4">
                                    {{ $riwayatPendaftaran->firstItem()
                                        + $loop->index }}
                                </td>

                                {{-- TANGGAL PENDAFTARAN --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    {{ $riwayat->created_at?->format('d/m/Y') ?? '-' }}
                                </td>

                                {{-- POLI --}}
                                <td class="px-5 py-4 font-medium">
                                    {{ $dokter?->poli?->nama_poli ?? '-' }}
                                </td>

                                {{-- DOKTER --}}
                                <td class="px-5 py-4">
                                    {{ $dokter?->nama ?? '-' }}
                                </td>

                                {{-- JADWAL --}}
                                <td class="px-5 py-4">
                                    <div class="font-medium">
                                        {{ $jadwal?->hari ?? '-' }}
                                    </div>

                                    @if($jadwal)
                                        <div class="text-xs text-slate-500 mt-1">
                                            {{ substr($jadwal->jam_mulai, 0, 5) }}
                                            -
                                            {{ substr($jadwal->jam_selesai, 0, 5) }}
                                        </div>
                                    @endif
                                </td>

                                {{-- NO ANTRIAN --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center
                                                 justify-center min-w-10
                                                 h-10 px-2 rounded-lg
                                                 bg-indigo-50 text-indigo-700
                                                 font-bold">
                                        {{ $riwayat->no_antrian }}
                                    </span>
                                </td>

                                {{-- STATUS --}}
                                <td class="px-5 py-4">
                                    @if($sudahDiperiksa)
                                        <span class="badge badge-success
                                                     text-white font-semibold
                                                     whitespace-nowrap">
                                            Sudah Diperiksa
                                        </span>
                                    @else
                                        <span class="badge badge-warning
                                                     font-semibold
                                                     whitespace-nowrap">
                                            Menunggu Pemeriksaan
                                        </span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="px-5 py-4 text-right">

                                    @if($sudahDiperiksa)

                                        <a href="{{ route('pasien.riwayat.show', $riwayat->id) }}"
                                           class="btn btn-sm btn-primary
                                                  rounded-lg gap-2">

                                            <i class="fas fa-eye"></i>
                                            Detail

                                        </a>

                                    @else

                                        <span class="text-xs text-slate-400">
                                            Belum tersedia
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8"
                                    class="px-6 py-14 text-center">

                                    <div class="flex flex-col
                                                items-center gap-3">

                                        <i class="fas fa-clock-rotate-left
                                                  text-4xl text-slate-300"></i>

                                        <h3 class="font-semibold
                                                   text-slate-700">
                                            Belum Ada Riwayat Pendaftaran
                                        </h3>

                                        <p class="text-sm text-slate-500">
                                            Anda belum pernah melakukan
                                            pendaftaran pemeriksaan.
                                        </p>

                                        <a href="{{ route('pasien.daftar') }}"
                                           class="btn btn-primary btn-sm mt-2">
                                            Daftar Pemeriksaan
                                        </a>

                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            @if($riwayatPendaftaran->hasPages())

                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $riwayatPendaftaran->links() }}
                </div>

            @endif

        </div>

    </div>

</x-layouts.app>
