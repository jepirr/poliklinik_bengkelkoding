
<x-layouts.app title="Tambah Jadwal Periksa">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('jadwal-periksa.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-lg
                  bg-slate-100 hover:bg-slate-200
                  text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <h2 class="text-2xl font-bold text-slate-800">
            Tambah Jadwal Periksa
        </h2>
    </div>

    {{-- Card Form --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('jadwal-periksa.store') }}"
                  method="POST" novalidate>
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    {{-- Hari --}}
                    <div>
                        <label for="hari"
                               class="block text-sm font-semibold text-slate-700 mb-2">
                            Hari Periksa <span class="text-red-500">*</span>
                        </label>

                        <select id="hari"
                                name="hari"
                                class="select select-bordered border-2 rounded-lg
                                       w-full focus:outline-none focus:ring-2
                                       focus:ring-primary
                                       @error('hari') select-error @enderror">

                            <option value="">Pilih Hari</option>

                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                <option value="{{ $hari }}"
                                    @selected(old('hari') === $hari)>
                                    {{ $hari }}
                                </option>
                            @endforeach
                        </select>

                        @error('hari')
                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status"
                               class="block text-sm font-semibold text-slate-700 mb-2">
                            Status Jadwal <span class="text-red-500">*</span>
                        </label>

                        <select id="status"
                                name="status"
                                class="select select-bordered border-2 rounded-lg
                                       w-full focus:outline-none focus:ring-2
                                       focus:ring-primary
                                       @error('status') select-error @enderror">

                            <option value="1" @selected(old('status', '1') == '1')>
                                Aktif
                            </option>
                            <option value="0" @selected(old('status', '1') == '0')>
                                Nonaktif
                            </option>
                        </select>

                        @error('status')
                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Jam Mulai --}}
                    <div>
                        <label for="jam_mulai"
                               class="block text-sm font-semibold text-slate-700 mb-2">
                            Jam Mulai <span class="text-red-500">*</span>
                        </label>

                        <input type="time"
                               id="jam_mulai"
                               name="jam_mulai"
                               value="{{ old('jam_mulai') }}"
                               class="input input-bordered border-2 rounded-lg
                                      w-full focus:outline-none focus:ring-2
                                      focus:ring-primary
                                      @error('jam_mulai') input-error @enderror">

                        @error('jam_mulai')
                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Jam Selesai --}}
                    <div>
                        <label for="jam_selesai"
                               class="block text-sm font-semibold text-slate-700 mb-2">
                            Jam Selesai <span class="text-red-500">*</span>
                        </label>

                        <input type="time"
                               id="jam_selesai"
                               name="jam_selesai"
                               value="{{ old('jam_selesai') }}"
                               class="input input-bordered border-2 rounded-lg
                                      w-full focus:outline-none focus:ring-2
                                      focus:ring-primary
                                      @error('jam_selesai') input-error @enderror">

                        @error('jam_selesai')
                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                {{-- Info --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl
                            px-4 py-3 mb-8 flex items-start gap-3">
                    <i class="fas fa-circle-info text-blue-500 mt-0.5"></i>
                    <p class="text-sm text-blue-700">
                        Pastikan jam selesai lebih besar daripada jam mulai.
                        Jadwal yang dibuat otomatis terhubung ke akun dokter
                        yang sedang login.
                    </p>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5
                                   bg-primary hover:bg-primary/90
                                   text-white rounded-lg font-semibold
                                   text-sm transition">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>

                    <a href="{{ route('jadwal-periksa.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5
                              bg-slate-100 hover:bg-slate-200
                              text-slate-600 rounded-xl font-semibold
                              text-sm transition">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

</x-layouts.app>
