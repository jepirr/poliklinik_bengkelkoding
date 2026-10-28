
<x-layouts.app title="Pembayaran">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Pembayaran
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Lihat tagihan pemeriksaan dan unggah bukti pembayaran.
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
                            <th class="px-5 py-4">Tanggal Periksa</th>
                            <th class="px-5 py-4">Poli</th>
                            <th class="px-5 py-4">Dokter</th>
                            <th class="px-5 py-4">Tagihan</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Bukti Pembayaran</th>
                        </tr>
                    </thead>

                    <tbody class="text-sm text-slate-700">

                        @forelse($tagihans as $tagihan)

                            @php
                                $pendaftaran = $tagihan->daftarPoli;
                                $dokter = $pendaftaran?->jadwalPeriksa?->dokter;
                                $pembayaran = $tagihan->pembayaran;
                            @endphp

                            <tr class="border-b border-slate-100
                                       hover:bg-slate-50">

                                <td class="px-5 py-4">
                                    {{ $tagihans->firstItem() + $loop->index }}
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    {{ $tagihan->tgl_periksa
                                        ? \Carbon\Carbon::parse($tagihan->tgl_periksa)->format('d/m/Y')
                                        : '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ $dokter?->poli?->nama_poli ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ $dokter?->nama ?? '-' }}
                                </td>

                                <td class="px-5 py-4 font-bold
                                           whitespace-nowrap">
                                    Rp {{ number_format(
                                        $tagihan->biaya_periksa,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                                <td class="px-5 py-4">

                                    @if(!$pembayaran)

                                        <span class="badge badge-warning">
                                            Belum Upload
                                        </span>

                                    @elseif($pembayaran->status === 'lunas')

                                        <span class="badge badge-success
                                                     text-white">
                                            Lunas
                                        </span>

                                    @else

                                        <span class="badge badge-info
                                                     text-white">
                                            Menunggu Verifikasi
                                        </span>

                                    @endif

                                </td>

                                <td class="px-5 py-4">

                                    @if(!$pembayaran)

                                        <form
                                            action="{{ route('pasien.pembayaran.store', $tagihan->id) }}"
                                            method="POST"
                                            enctype="multipart/form-data"
                                            class="flex flex-col gap-2 min-w-52">

                                            @csrf

                                            <input type="hidden"
                                                   name="id_periksa"
                                                   value="{{ $tagihan->id }}">

                                            <input
                                                type="file"
                                                name="bukti_pembayaran"
                                                accept="image/jpeg,image/png,image/webp"
                                                required
                                                class="file-input file-input-bordered
                                                       file-input-sm w-full">

                                            @if(
                                                $errors->has('bukti_pembayaran')
                                                &&
                                                (int) old('id_periksa') ===
                                                (int) $tagihan->id
                                            )
                                                <p class="text-xs text-red-600">
                                                    {{ $errors->first('bukti_pembayaran') }}
                                                </p>
                                            @endif

                                            <button
                                                type="submit"
                                                class="btn btn-primary btn-sm">

                                                <i class="fas fa-upload"></i>
                                                Upload Bukti

                                            </button>

                                        </form>

                                        <p class="text-xs text-slate-400 mt-2">
                                            JPG, PNG, WEBP. Maks. 4 MB.
                                        </p>

                                    @elseif($pembayaran->status === 'lunas')

                                        <span class="text-sm text-green-700
                                                     font-semibold">
                                            Pembayaran selesai
                                        </span>

                                    @else

                                        <span class="text-sm text-slate-500">
                                            Bukti sudah dikirim
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7"
                                    class="py-14 px-6 text-center
                                           text-slate-500">

                                    <i class="fas fa-receipt
                                              text-3xl text-slate-300
                                              block mb-3"></i>

                                    Belum ada tagihan pemeriksaan.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

            @if($tagihans->hasPages())
                <div class="p-5 border-t border-slate-200">
                    {{ $tagihans->links() }}
                </div>
            @endif

        </div>
    </div>

</x-layouts.app>
