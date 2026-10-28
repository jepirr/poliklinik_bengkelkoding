
<x-layouts.app title="Verifikasi Pembayaran">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Verifikasi Pembayaran
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Daftar bukti pembayaran yang menunggu verifikasi admin.
        </p>
    </div>

    <div class="card bg-base-100 border border-slate-200
                shadow-md rounded-2xl">

        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table w-full">

                    <thead class="bg-slate-100 text-slate-600
                                 text-xs uppercase">
                        <tr>
                            <th class="px-5 py-4">No.</th>
                            <th class="px-5 py-4">Pasien</th>
                            <th class="px-5 py-4">Poli</th>
                            <th class="px-5 py-4">Total Tagihan</th>
                            <th class="px-5 py-4">Tanggal Upload</th>
                            <th class="px-5 py-4">Foto Bukti</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="text-sm text-slate-700">

                        @forelse($pembayarans as $pembayaran)

                            @php
                                $periksa = $pembayaran->periksa;
                                $pendaftaran = $periksa?->daftarPoli;
                            @endphp

                            <tr class="border-b border-slate-100
                                       hover:bg-slate-50">

                                <td class="px-5 py-4">
                                    {{ $pembayarans->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4 font-semibold">
                                    {{ $pendaftaran?->pasien?->nama ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ $pendaftaran?->jadwalPeriksa?->dokter?->poli?->nama_poli ?? '-' }}
                                </td>

                                <td class="px-5 py-4 font-bold
                                           whitespace-nowrap">
                                    Rp {{ number_format(
                                        $periksa?->biaya_periksa ?? 0,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    {{ $pembayaran->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>

                                <td class="px-5 py-4">

                                    <a href="{{ route('admin.pembayaran.bukti', $pembayaran->id) }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-block">

                                        <img
                                            src="{{ route('admin.pembayaran.bukti', $pembayaran->id) }}"
                                            alt="Bukti pembayaran pasien"
                                            loading="lazy"
                                            class="w-24 h-24 object-cover
                                                   rounded-xl border border-slate-200
                                                   hover:opacity-80 transition">

                                    </a>

                                    <a href="{{ route('admin.pembayaran.bukti', $pembayaran->id) }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="text-xs text-indigo-600
                                              hover:underline block mt-2">

                                        Lihat Bukti

                                    </a>

                                </td>

                                <td class="px-5 py-4 text-right">

                                    <form
                                        action="{{ route('admin.pembayaran.verifikasi', $pembayaran->id) }}"
                                        method="POST">

                                        @csrf

                                        <button
                                            type="submit"
                                            class="btn btn-success btn-sm
                                                   text-white whitespace-nowrap">

                                            <i class="fas fa-check-circle"></i>
                                            Konfirmasi Lunas

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7"
                                    class="px-6 py-14 text-center
                                           text-slate-500">

                                    <i class="fas fa-circle-check
                                              text-3xl text-slate-300
                                              block mb-3"></i>

                                    Tidak ada pembayaran yang menunggu verifikasi.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

            @if($pembayarans->hasPages())
                <div class="p-5 border-t border-slate-200">
                    {{ $pembayarans->links() }}
                </div>
            @endif

        </div>
    </div>

</x-layouts.app>
