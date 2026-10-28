
<x-layouts.app title="Dashboard Pasien">

    {{-- HEADER --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Dashboard Pasien
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Informasi antrean dan jadwal pemeriksaan poliklinik.
        </p>
    </div>

    {{-- BANNER ANTREAN AKTIF --}}
    @if($antreanAktif)

        @php
            $jadwalAktif = $antreanAktif->jadwalPeriksa;
            $dokterAktif = $jadwalAktif?->dokter;
        @endphp

        <div class="card bg-base-100 border border-slate-200
                    rounded-2xl shadow-md mb-8">

            <div class="card-body p-6">

                <div class="flex flex-col sm:flex-row
                            sm:items-center sm:justify-between
                            gap-3 mb-5">

                    <div>
                        <h3 class="text-lg font-bold text-slate-800">
                            Antrean Aktif Anda
                        </h3>

                        <p class="text-sm text-slate-500 mt-1">
                            Informasi pendaftaran pemeriksaan Anda.
                        </p>
                    </div>

                    <span class="badge badge-warning font-semibold">
                        Menunggu Pemeriksaan
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2
                            xl:grid-cols-3 gap-4">

                    {{-- POLI --}}
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-500 mb-2">
                            Poliklinik
                        </p>

                        <p class="font-semibold text-slate-800">
                            {{ $dokterAktif?->poli?->nama_poli ?? '-' }}
                        </p>
                    </div>

                    {{-- DOKTER --}}
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-500 mb-2">
                            Dokter
                        </p>

                        <p class="font-semibold text-slate-800">
                            {{ $dokterAktif?->nama ?? '-' }}
                        </p>
                    </div>

                    {{-- JADWAL --}}
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-500 mb-2">
                            Jadwal Periksa
                        </p>

                        <p class="font-semibold text-slate-800">
                            {{ $jadwalAktif?->hari ?? '-' }}
                        </p>

                        <p class="text-sm text-slate-600 mt-1">
                            @if($jadwalAktif)
                                {{ substr($jadwalAktif->jam_mulai, 0, 5) }}
                                -
                                {{ substr($jadwalAktif->jam_selesai, 0, 5) }}
                            @else
                                -
                            @endif
                        </p>
                    </div>

                </div>

                {{-- NOMOR ANTREAN --}}
                <div class="grid grid-cols-1 md:grid-cols-2
                            gap-4 mt-5">

                    <div class="rounded-xl bg-indigo-50
                                border border-indigo-100 p-5
                                text-center">

                        <p class="text-sm font-semibold text-indigo-700">
                            Nomor Antrean Anda
                        </p>

                        <p class="text-4xl font-extrabold
                                  text-indigo-700 mt-3">
                            {{ $antreanAktif->no_antrian }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-emerald-50
                                border border-emerald-100 p-5
                                text-center">

                        <p class="text-sm font-semibold text-emerald-700">
                            Nomor Sedang Dilayani
                        </p>


                        <p class="text-4xl font-extrabold text-emerald-700 mt-3"
                        data-antrean-aktif="1"
                        data-antrian-jadwal="{{ $jadwalAktif?->id }}">

                            {{ $jadwalAktif?->no_antrian_dilayani ?? '—' }}

                        </p>

                        <p id="keterangan-antrian-aktif"
                        class="text-xs text-emerald-700 mt-2
                                {{ is_null($jadwalAktif?->no_antrian_dilayani) ? '' : 'hidden' }}">

                            Belum ada antrean yang dipanggil

                        </p>


                        @if(is_null($jadwalAktif?->no_antrian_dilayani))
                            <p class="text-xs text-emerald-700 mt-2">
                                Belum ada antrean yang dipanggil
                            </p>
                        @endif

                    </div>

                </div>

            </div>

        </div>

    @else

        {{-- PASIEN TANPA ANTREAN AKTIF --}}
        <div class="card bg-base-100 border border-slate-200
                    rounded-2xl shadow-sm mb-8">

            <div class="card-body items-center text-center py-9">

                <i class="fas fa-clipboard-list
                          text-3xl text-slate-400 mb-2"></i>

                <h3 class="font-bold text-slate-800">
                    Belum Ada Antrean Aktif
                </h3>

                <p class="text-sm text-slate-500">
                    Anda belum memiliki pendaftaran pemeriksaan
                    yang sedang berlangsung.
                </p>

                <a href="{{ route('pasien.daftar') }}"
                   class="btn btn-primary btn-sm mt-3">
                    Daftar Pemeriksaan
                </a>

            </div>

        </div>

    @endif


    {{-- TABEL JADWAL POLIKLINIK --}}
    <div class="card bg-base-100 rounded-2xl
                border border-slate-200 shadow-md">

        <div class="card-body p-0">

            <div class="p-6 border-b border-slate-200">

                <h3 class="text-lg font-bold text-slate-800">
                    Daftar Jadwal Poliklinik
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Informasi jadwal dokter dan antrean
                    yang sedang dilayani.
                </p>

            </div>

            <div class="overflow-x-auto">

                <table class="table w-full">

                    <thead class="bg-slate-100
                                 text-slate-600 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-4">No.</th>
                            <th class="px-6 py-4">Poli</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Hari</th>
                            <th class="px-6 py-4">Jam Periksa</th>
                            <th class="px-6 py-4 text-center">
                                Sedang Dilayani
                            </th>
                        </tr>
                    </thead>

                    <tbody class="text-sm text-slate-700">

                        @forelse($jadwals as $jadwal)

                            <tr class="border-b border-slate-100
                                       hover:bg-slate-50 transition">

                                <td class="px-6 py-4">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-6 py-4 font-medium">
                                    {{ $jadwal->dokter?->poli?->nama_poli ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $jadwal->dokter?->nama ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $jadwal->hari }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ substr($jadwal->jam_mulai, 0, 5) }}
                                    -
                                    {{ substr($jadwal->jam_selesai, 0, 5) }}
                                </td>

                                <td class="px-6 py-4 text-center">

                                    <span
                                        class="inline-flex items-center
                                               justify-center min-w-10
                                               h-10 rounded-lg bg-indigo-50
                                               text-indigo-700 font-bold"
                                        data-antrian-jadwal="{{ $jadwal->id }}">

                                        {{ $jadwal->no_antrian_dilayani ?? '—' }}

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6"
                                    class="text-center py-12
                                           text-slate-500">

                                    Belum ada jadwal poliklinik.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-layouts.app>
