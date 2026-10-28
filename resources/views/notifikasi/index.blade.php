
<x-layouts.app title="Notifikasi">

    {{-- HEADER --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Notifikasi
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Informasi terbaru terkait pembayaran pemeriksaan.
        </p>
    </div>

    {{-- DAFTAR NOTIFIKASI --}}
    <div class="card bg-base-100 border border-slate-200
                shadow-md rounded-2xl">

        <div class="card-body p-0">

            <div class="px-6 py-5 border-b border-slate-200
                        flex items-center justify-between gap-3">

                <h3 class="font-bold text-slate-800">
                    Daftar Notifikasi
                </h3>

                <span class="badge badge-primary">
                    {{ $jumlahBelumDibaca }} belum dibaca
                </span>

            </div>

            <div class="divide-y divide-slate-100">

                @forelse($notifikasis as $notifikasi)

                    @php
                        $belumDibaca = is_null($notifikasi->read_at);

                        $judul = $notifikasi->data['judul']
                            ?? 'Notifikasi';

                        $pesan = $notifikasi->data['pesan']
                            ?? '-';
                    @endphp

                    <div class="p-5 md:p-6
                                {{ $belumDibaca ? 'bg-indigo-50/60' : 'bg-white' }}">

                        <div class="flex flex-col md:flex-row
                                    md:items-start gap-4">

                            {{-- ICON --}}
                            <div class="w-11 h-11 rounded-xl
                                        flex items-center justify-center
                                        shrink-0
                                        {{ $belumDibaca
                                            ? 'bg-indigo-100 text-indigo-700'
                                            : 'bg-slate-100 text-slate-500' }}">

                                <i class="fas fa-bell text-lg"></i>

                            </div>

                            {{-- KONTEN --}}
                            <div class="flex-1 min-w-0">

                                <div class="flex flex-wrap
                                            items-center gap-2">

                                    <h4 class="font-bold text-slate-800">
                                        {{ $judul }}
                                    </h4>

                                    @if($belumDibaca)

                                        <span class="badge badge-primary
                                                     badge-sm">
                                            Baru
                                        </span>

                                    @else

                                        <span class="badge badge-ghost
                                                     badge-sm">
                                            Sudah Dibaca
                                        </span>

                                    @endif

                                </div>

                                <p class="text-sm text-slate-600 mt-2
                                          leading-relaxed">
                                    {{ $pesan }}
                                </p>

                                <p class="text-xs text-slate-400 mt-3">
                                    <i class="fas fa-clock mr-1"></i>

                                    {{ $notifikasi->created_at
                                        ? $notifikasi->created_at->format('d/m/Y H:i')
                                        : '-' }}
                                </p>

                            </div>

                            {{-- TOMBOL TANDAI DIBACA --}}
                            <div class="shrink-0">

                                @if($belumDibaca)

                                    <form
                                        method="POST"
                                        action="{{ route('notifikasi.baca', $notifikasi->id) }}">

                                        @csrf

                                        <button type="submit"
                                                class="btn btn-sm btn-outline btn-primary">

                                            <i class="fas fa-check"></i>
                                            Tandai Dibaca

                                        </button>

                                    </form>

                                @else

                                    <span class="text-xs text-slate-400">
                                        <i class="fas fa-check-double mr-1"></i>
                                        Dibaca
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    {{-- EMPTY STATE --}}
                    <div class="py-16 px-6 text-center">

                        <div class="w-16 h-16 mx-auto
                                    rounded-full bg-slate-100
                                    flex items-center justify-center
                                    mb-4">

                            <i class="fas fa-bell-slash
                                      text-2xl text-slate-400"></i>

                        </div>

                        <h3 class="font-bold text-slate-700">
                            Belum Ada Notifikasi
                        </h3>

                        <p class="text-sm text-slate-500 mt-2">
                            Notifikasi pembayaran akan tampil
                            di sini ketika tersedia.
                        </p>

                    </div>

                @endforelse

            </div>

            {{-- PAGINATION --}}
            @if($notifikasis->hasPages())

                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $notifikasis->links() }}
                </div>

            @endif

        </div>

    </div>

</x-layouts.app>
