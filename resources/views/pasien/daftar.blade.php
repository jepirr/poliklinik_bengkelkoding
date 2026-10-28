
<x-layouts.app title="Pendaftaran Poli">

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">
            Pendaftaran Poli
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Pilih poli dan jadwal pemeriksaan yang tersedia.
        </p>
    </div>

    {{-- Pesan validasi umum --}}
    @if($errors->any())
        <div role="alert"
             class="bg-red-50 border border-red-200
                    text-red-700 rounded-xl px-4 py-3 mb-6">

            <div class="flex items-center gap-2 font-semibold text-sm">
                <i class="fas fa-circle-exclamation"></i>
                Pendaftaran belum berhasil.
            </div>

            <ul class="list-disc pl-6 mt-2 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Jika pasien masih mempunyai antrian --}}
    @if($pendaftaranAktif)

        <div class="card bg-base-100 shadow-md
                    rounded-2xl border border-slate-200">

            <div class="card-body p-8">

                <div class="flex items-center gap-3 mb-5">
                    <div class="w-12 h-12 rounded-xl
                                bg-amber-100 text-amber-600
                                flex items-center justify-center">
                        <i class="fas fa-clock text-xl"></i>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-slate-800">
                            Anda Memiliki Antrian Aktif
                        </h3>

                        <p class="text-sm text-slate-500">
                            Selesaikan pemeriksaan sebelum
                            mendaftar kembali.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-500 mb-1">
                            Poli
                        </p>

                        <p class="font-semibold text-slate-800">
                            {{
                                $pendaftaranAktif->jadwalPeriksa?->dokter?->poli?->nama_poli
                                ?? '-'
                            }}
                        </p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-500 mb-1">
                            Dokter
                        </p>

                        <p class="font-semibold text-slate-800">
                            {{
                                $pendaftaranAktif->jadwalPeriksa?->dokter?->nama
                                ?? '-'
                            }}
                        </p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-xs text-slate-500 mb-1">
                            Jadwal Periksa
                        </p>

                        <p class="font-semibold text-slate-800">
                            {{ $pendaftaranAktif->jadwalPeriksa?->hari ?? '-' }}

                            {{ substr($pendaftaranAktif->jadwalPeriksa?->jam_mulai ?? '', 0, 5) }}
                            -
                            {{ substr($pendaftaranAktif->jadwalPeriksa?->jam_selesai ?? '', 0, 5) }}
                        </p>
                    </div>

                    <div class="bg-blue-50 rounded-xl p-4">
                        <p class="text-xs text-blue-600 mb-1">
                            Nomor Antrian Anda
                        </p>

                        <p class="text-2xl font-bold text-blue-700">
                            {{ $pendaftaranAktif->no_antrian }}
                        </p>
                    </div>

                </div>

                <div class="mt-5">
                    <p class="text-sm text-slate-600">
                        <span class="font-semibold">Keluhan:</span>
                        {{ $pendaftaranAktif->keluhan }}
                    </p>
                </div>

                <div class="mt-6">
                    <a href="{{ route('pasien.dashboard') }}"
                       class="inline-flex items-center gap-2
                              px-5 py-2.5 bg-primary
                              hover:bg-primary/90 text-white
                              rounded-lg text-sm font-semibold transition">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Dashboard
                    </a>
                </div>

            </div>
        </div>

    {{-- Jika tidak ada jadwal yang tersedia --}}
    @elseif($jadwals->isEmpty())

        <div class="card bg-base-100 shadow-md
                    rounded-2xl border border-slate-200">

            <div class="card-body py-14 text-center">

                <i class="fas fa-calendar-xmark
                          text-4xl text-slate-300 mb-3"></i>

                <h3 class="text-lg font-semibold text-slate-700">
                    Belum Ada Jadwal Tersedia
                </h3>

                <p class="text-sm text-slate-500 mt-2">
                    Belum terdapat jadwal dokter aktif
                    yang terhubung dengan poli.
                </p>

            </div>
        </div>

    {{-- Form pendaftaran --}}
    @else

        <div class="card bg-base-100 shadow-md
                    rounded-2xl border border-slate-200">

            <div class="card-body p-6 md:p-8">

                <form action="{{ route('pasien.daftar.submit') }}"
                      method="POST" novalidate>

                    @csrf

                    {{-- Informasi pasien --}}
                    <div class="mb-6">
                        <h3 class="text-base font-bold text-slate-800 mb-3">
                            Informasi Pasien
                        </h3>

                        <div class="bg-slate-50 border border-slate-200
                                    rounded-xl p-4 grid
                                    grid-cols-1 md:grid-cols-2 gap-4">

                            <div>
                                <p class="text-xs text-slate-500">
                                    Nama Pasien
                                </p>
                                <p class="text-sm font-semibold text-slate-800">
                                    {{ $user->nama }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-500">
                                    Nomor Rekam Medis
                                </p>
                                <p class="text-sm font-semibold text-slate-800">
                                    {{ $user->no_rm ?? '-' }}
                                </p>
                            </div>

                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2
                                gap-6 mb-6">

                        {{-- Pilih Poli --}}
                        <div>
                            <label for="id_poli"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">
                                Pilih Poli
                                <span class="text-red-500">*</span>
                            </label>

                            <select name="id_poli"
                                    id="id_poli"
                                    class="select select-bordered
                                           border-2 rounded-lg w-full
                                           focus:outline-none
                                           focus:ring-2 focus:ring-primary
                                           @error('id_poli') select-error @enderror">

                                <option value="">Pilih Poli</option>

                                @foreach($polis as $poli)
                                    <option value="{{ $poli->id }}"
                                        @selected((string) old('id_poli') === (string) $poli->id)>
                                        {{ $poli->nama_poli }}
                                    </option>
                                @endforeach

                            </select>

                            @error('id_poli')
                                <p class="text-xs text-red-500 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Pilih Jadwal --}}
                        <div>
                            <label for="id_jadwal"
                                   class="block text-sm font-semibold
                                          text-slate-700 mb-2">
                                Pilih Jadwal
                                <span class="text-red-500">*</span>
                            </label>

                            <select name="id_jadwal"
                                    id="id_jadwal"
                                    class="select select-bordered
                                           border-2 rounded-lg w-full
                                           focus:outline-none
                                           focus:ring-2 focus:ring-primary
                                           @error('id_jadwal') select-error @enderror">

                                <option value="">Pilih Jadwal</option>

                                @foreach($jadwals as $jadwal)
                                    <option value="{{ $jadwal->id }}"
                                            data-id-poli="{{ $jadwal->dokter->id_poli }}"
                                            @selected((string) old('id_jadwal') === (string) $jadwal->id)>

                                        {{ $jadwal->dokter->nama }}
                                        —
                                        {{ $jadwal->hari }}
                                        ({{ substr($jadwal->jam_mulai, 0, 5) }}
                                        -
                                        {{ substr($jadwal->jam_selesai, 0, 5) }})

                                    </option>
                                @endforeach

                            </select>

                            @error('id_jadwal')
                                <p class="text-xs text-red-500 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- Keluhan --}}
                    <div class="mb-8">
                        <label for="keluhan"
                               class="block text-sm font-semibold
                                      text-slate-700 mb-2">
                            Keluhan
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea name="keluhan"
                                  id="keluhan"
                                  rows="4"
                                  placeholder="Jelaskan keluhan yang dialami..."
                                  class="textarea textarea-bordered
                                         border-2 rounded-lg p-3 w-full
                                         focus:outline-none
                                         focus:ring-2 focus:ring-primary
                                         @error('keluhan') textarea-error @enderror">{{ old('keluhan') }}</textarea>

                        @error('keluhan')
                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Tombol --}}
                    <div class="flex items-center gap-3">

                        <button type="submit"
                                class="inline-flex items-center gap-2
                                       px-6 py-2.5 bg-primary
                                       hover:bg-primary/90 text-white
                                       rounded-lg text-sm font-semibold
                                       transition">
                            <i class="fas fa-save"></i>
                            Daftar Poli
                        </button>

                        <a href="{{ route('pasien.dashboard') }}"
                           class="inline-flex items-center gap-2
                                  px-6 py-2.5 bg-slate-100
                                  hover:bg-slate-200 text-slate-600
                                  rounded-lg text-sm font-semibold
                                  transition">
                            Batal
                        </a>

                    </div>

                </form>

            </div>
        </div>

    @endif

    {{-- Filter jadwal sesuai poli --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const poliSelect = document.getElementById('id_poli');
                const jadwalSelect = document.getElementById('id_jadwal');

                if (!poliSelect || !jadwalSelect) {
                    return;
                }

                const pilihanJadwal = Array.from(
                    jadwalSelect.querySelectorAll('option[data-id-poli]')
                );

                function filterJadwal() {
                    const idPoli = poliSelect.value;

                    pilihanJadwal.forEach(function (option) {
                        const sesuaiPoli =
                            idPoli !== '' &&
                            option.dataset.idPoli === idPoli;

                        option.hidden = !sesuaiPoli;
                        option.disabled = !sesuaiPoli;
                    });

                    const terpilihValid = pilihanJadwal.some(
                        option =>
                            option.value === jadwalSelect.value &&
                            !option.disabled
                    );

                    if (!terpilihValid) {
                        jadwalSelect.value = '';
                    }

                    jadwalSelect.disabled = idPoli === '';
                }

                poliSelect.addEventListener('change', function () {
                    jadwalSelect.value = '';
                    filterJadwal();
                });

                filterJadwal();
            });
        </script>
    @endpush

</x-layouts.app>
