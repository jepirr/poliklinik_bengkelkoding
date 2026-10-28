
<x-layouts.app title="Detail Riwayat Pendaftaran">

    {{-- HEADER --}}
    <div class="flex items-center gap-3 mb-6">

        <a href="{{ route('pasien.riwayat.index') }}"
           class="inline-flex items-center justify-center
                  w-10 h-10 rounded-xl bg-slate-100
                  text-slate-600 hover:bg-slate-200 transition">

            <i class="fas fa-arrow-left"></i>
        </a>

        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Detail Riwayat Pemeriksaan
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Hasil pemeriksaan dari pendaftaran Anda.
            </p>
        </div>

    </div>

    <div class="space-y-6">

        {{-- INFORMASI PENDAFTARAN --}}
        <div class="card bg-base-100 rounded-2xl
                    border border-slate-200 shadow-sm">

            <div class="card-body p-0">

                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="font-bold text-slate-800">
                        Informasi Pendaftaran
                    </h3>
                </div>

                <div class="p-6 grid grid-cols-1
                            md:grid-cols-2 gap-5">

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Poliklinik
                        </p>

                        <p class="font-semibold text-slate-800">
                            {{ $pendaftaran->jadwalPeriksa?->dokter?->poli?->nama_poli ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Dokter
                        </p>

                        <p class="font-semibold text-slate-800">
                            {{ $pendaftaran->jadwalPeriksa?->dokter?->nama ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Jadwal Dokter
                        </p>

                        <p class="font-semibold text-slate-800">
                            {{ $pendaftaran->jadwalPeriksa?->hari ?? '-' }}

                            @if($pendaftaran->jadwalPeriksa)
                                ,
                                {{ substr($pendaftaran->jadwalPeriksa->jam_mulai, 0, 5) }}
                                -
                                {{ substr($pendaftaran->jadwalPeriksa->jam_selesai, 0, 5) }}
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Nomor Antrean
                        </p>

                        <p class="font-bold text-indigo-700 text-lg">
                            {{ $pendaftaran->no_antrian }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Tanggal Pendaftaran
                        </p>

                        <p class="font-semibold text-slate-800">
                            {{ $pendaftaran->created_at?->format('d/m/Y H:i') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500 mb-1">
                            Tanggal Pemeriksaan
                        </p>

                        <p class="font-semibold text-slate-800">
                            {{ $periksa->tgl_periksa
                                ? \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i')
                                : '-' }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-slate-500 mb-1">
                            Keluhan Pasien
                        </p>

                        <p class="text-sm text-slate-700">
                            {{ $pendaftaran->keluhan }}
                        </p>
                    </div>

                </div>

            </div>

        </div>

        {{-- CATATAN DOKTER --}}
        <div class="card bg-base-100 rounded-2xl
                    border border-slate-200 shadow-sm">

            <div class="card-body p-0">

                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="font-bold text-slate-800">
                        Catatan Dokter
                    </h3>
                </div>

                <div class="p-6">
                    <p class="text-sm text-slate-700 whitespace-pre-line">
                        {{ $periksa->catatan ?: 'Tidak ada catatan pemeriksaan.' }}
                    </p>
                </div>

            </div>

        </div>

        {{-- OBAT YANG DIRESEPKAN --}}
        <div class="card bg-base-100 rounded-2xl
                    border border-slate-200 shadow-sm">

            <div class="card-body p-0">

                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="font-bold text-slate-800">
                        Obat yang Diresepkan
                    </h3>
                </div>

                <div class="overflow-x-auto">

                    <table class="table w-full">

                        <thead class="bg-slate-100 text-slate-600
                                     text-xs uppercase">
                            <tr>
                                <th class="px-6 py-4">No.</th>
                                <th class="px-6 py-4">Nama Obat</th>
                                <th class="px-6 py-4">Kemasan</th>
                                <th class="px-6 py-4 text-right">Harga</th>
                            </tr>
                        </thead>

                        <tbody class="text-sm text-slate-700">

                            @forelse($periksa->detailPeriksas as $detail)

                                <tr class="border-b border-slate-100">

                                    <td class="px-6 py-4">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-4 font-semibold">
                                        {{ $detail->obat?->nama_obat ?? 'Obat tidak tersedia' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ $detail->obat?->kemasan ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        @if($detail->obat)
                                            Rp {{ number_format($detail->obat->harga, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4"
                                        class="px-6 py-10 text-center text-slate-500">
                                        Tidak ada obat yang diresepkan.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        {{-- TOTAL BIAYA --}}
        <div class="card bg-base-100 rounded-2xl
                    border border-indigo-100 shadow-sm">

            <div class="card-body p-6">

                <div class="flex flex-col sm:flex-row
                            sm:items-center sm:justify-between gap-3">

                    <div>
                        <h3 class="font-bold text-slate-800">
                            Total Biaya Pemeriksaan
                        </h3>

                        <p class="text-xs text-slate-500 mt-1">
                            Total biaya yang tercatat dari
                            hasil pemeriksaan dokter.
                        </p>
                    </div>

                    <p class="text-2xl font-extrabold text-indigo-700">
                        Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                    </p>

                </div>

            </div>

        </div>

    </div>

</x-layouts.app>
