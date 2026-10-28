<x-layouts.app title="Tambah Obat">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('obat.index') }}" class="flex items-center justify-center w-9 h-9 rounded-lg
                  bg-slate-100 hover:bg-slate-200
                  text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <h2 class="text-2xl font-bold text-slate-800">
            Tambah Obat
        </h2>
    </div>

    {{-- Card Form --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('obat.store') }}" method="POST" novalidate>
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    {{-- Nama Obat --}}
                    <div>
                        <label for="nama_obat" class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Obat <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="nama_obat" name="nama_obat" value="{{ old('nama_obat') }}"
                            placeholder="Masukkan nama obat..." class="input input-bordered border-2 rounded-lg p-2 w-full
                                      focus:outline-none focus:ring-2 focus:ring-primary
                                      @error('nama_obat') input-error @enderror">

                        @error('nama_obat')
                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Kemasan --}}
                    <div>
                        <label for="kemasan" class="block text-sm font-semibold text-slate-700 mb-2">
                            Kemasan <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="kemasan" name="kemasan" value="{{ old('kemasan') }}"
                            placeholder="Contoh: Strip 10 tablet" class="input input-bordered border-2 rounded-lg p-2 w-full
                                      focus:outline-none focus:ring-2 focus:ring-primary
                                      @error('kemasan') input-error @enderror">

                        @error('kemasan')
                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>


                {{-- Stok Obat --}}
                <div class="mb-6">
                    <label for="stok" class="block text-sm font-semibold text-slate-700 mb-2">
                        Stok Obat <span class="text-red-500">*</span>
                    </label>

                    <input type="number" id="stok" name="stok" value="{{ old('stok', 0) }}" min="0" step="1" required
                        class="input input-bordered border-2 rounded-lg
                  p-2 w-full
                  @error('stok') input-error @enderror">

                    @error('stok')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- Harga --}}
                <div class="mb-8">
                    <label for="harga" class="block text-sm font-semibold text-slate-700 mb-2">
                        Harga Obat (Rp) <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2
                                     text-sm font-semibold text-slate-500">
                            Rp
                        </span>

                        <input type="number" id="harga" name="harga" value="{{ old('harga') }}" min="0" step="0.01"
                            placeholder="15000" class="input input-bordered border-2 rounded-lg
                                      pl-12 pr-4 w-full
                                      focus:outline-none focus:ring-2 focus:ring-primary
                                      @error('harga') input-error @enderror">
                    </div>

                    @error('harga')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5
                                   bg-primary hover:bg-primary/90
                                   text-white rounded-lg font-semibold
                                   text-sm transition">
                        <i class="fas fa-save text-sm"></i>
                        Simpan
                    </button>

                    <a href="{{ route('obat.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5
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