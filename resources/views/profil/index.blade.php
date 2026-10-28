
<x-layouts.app title="Profil dan Ganti Password">

    {{-- HEADER --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-base-content">
            Profil dan Ganti Password
        </h2>

        <p class="text-sm text-base-content/60 mt-1">
            Informasi akun dan pengaturan keamanan password.
        </p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- ================= PROFIL ================= --}}
        <div class="xl:col-span-1">

            <div class="card bg-base-100 border border-base-300
                        shadow-sm rounded-2xl">

                <div class="card-body p-6">

                    <h3 class="text-lg font-bold text-base-content mb-4">
                        Informasi Profil
                    </h3>

                    {{-- AVATAR --}}
                    <div class="flex flex-col items-center
                                text-center mb-6">

                        <div class="w-20 h-20 rounded-full
                                    bg-primary text-primary-content
                                    flex items-center justify-center
                                    shadow-sm mb-3">

                            <span class="text-3xl font-bold">
                                {{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}
                            </span>

                        </div>

                        <h4 class="font-bold text-base-content text-lg">
                            {{ $user->nama ?? '-' }}
                        </h4>

                        <span class="badge badge-primary mt-2 capitalize">
                            {{ ucfirst($user->role) }}
                        </span>

                    </div>

                    <div class="space-y-4">

                        {{-- NAMA --}}
                        <div class="border-b border-base-200 pb-3">
                            <p class="text-xs text-base-content/50 mb-1">
                                Nama Lengkap
                            </p>

                            <p class="font-semibold text-base-content
                                      break-words">
                                {{ $user->nama ?? '-' }}
                            </p>
                        </div>

                        {{-- EMAIL --}}
                        <div class="border-b border-base-200 pb-3">
                            <p class="text-xs text-base-content/50 mb-1">
                                Email
                            </p>

                            <p class="font-semibold text-base-content
                                      break-all">
                                {{ $user->email }}
                            </p>
                        </div>

                        {{-- ROLE --}}
                        <div class="border-b border-base-200 pb-3">
                            <p class="text-xs text-base-content/50 mb-1">
                                Role Akun
                            </p>

                            <p class="font-semibold text-base-content">
                                {{ ucfirst($user->role) }}
                            </p>
                        </div>

                        {{-- NOMOR HP --}}
                        <div class="border-b border-base-200 pb-3">
                            <p class="text-xs text-base-content/50 mb-1">
                                Nomor HP
                            </p>

                            <p class="font-semibold text-base-content">
                                {{ $user->no_hp ?: '-' }}
                            </p>
                        </div>

                        {{-- REKAM MEDIS KHUSUS PASIEN --}}
                        @if($user->role === 'pasien')

                            <div class="border-b border-base-200 pb-3">
                                <p class="text-xs text-base-content/50 mb-1">
                                    Nomor Rekam Medis
                                </p>

                                <p class="font-semibold text-base-content">
                                    {{ $user->no_rm ?: '-' }}
                                </p>
                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>

        {{-- ================= GANTI PASSWORD ================= --}}
        <div class="xl:col-span-2">

            <div class="card bg-base-100 border border-base-300
                        shadow-sm rounded-2xl">

                <div class="card-body p-6 md:p-8">

                    <div class="flex items-center gap-3 mb-6">

                        <div class="w-12 h-12 rounded-xl bg-primary/10
                                    text-primary flex items-center
                                    justify-center">

                            <i class="fas fa-lock text-xl"></i>

                        </div>

                        <div>
                            <h3 class="text-lg font-bold
                                       text-base-content">
                                Ganti Password
                            </h3>

                            <p class="text-sm text-base-content/60 mt-1">
                                Perbarui password akun Anda.
                            </p>
                        </div>

                    </div>

                    <form
                        action="{{ route('profil.password.update') }}"
                        method="POST"
                        class="space-y-5">

                        @csrf
                        @method('PATCH')

                        {{-- PASSWORD LAMA --}}
                        <div>
                            <label for="current_password"
                                   class="block text-sm font-semibold
                                          text-base-content mb-2">

                                Password Lama
                                <span class="text-error">*</span>

                            </label>

                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                autocomplete="current-password"
                                required
                                class="input input-bordered w-full
                                       rounded-xl
                                       @error('current_password')
                                           input-error
                                       @enderror"
                                placeholder="Masukkan password saat ini">

                            @error('current_password')
                                <p class="text-error text-xs mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- PASSWORD BARU --}}
                        <div>
                            <label for="new_password"
                                   class="block text-sm font-semibold
                                          text-base-content mb-2">

                                Password Baru
                                <span class="text-error">*</span>

                            </label>

                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                autocomplete="new-password"
                                minlength="8"
                                required
                                class="input input-bordered w-full
                                       rounded-xl
                                       @error('new_password')
                                           input-error
                                       @enderror"
                                placeholder="Masukkan password baru">

                            <p class="text-xs text-base-content/50 mt-2">
                                Minimal 8 karakter dan harus berbeda
                                dari password sebelumnya.
                            </p>

                            @error('new_password')
                                <p class="text-error text-xs mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- KONFIRMASI PASSWORD --}}
                        <div>
                            <label for="new_password_confirmation"
                                   class="block text-sm font-semibold
                                          text-base-content mb-2">

                                Konfirmasi Password Baru
                                <span class="text-error">*</span>

                            </label>

                            <input
                                type="password"
                                id="new_password_confirmation"
                                name="new_password_confirmation"
                                autocomplete="new-password"
                                minlength="8"
                                required
                                class="input input-bordered w-full
                                       rounded-xl
                                       @error('new_password_confirmation')
                                           input-error
                                       @enderror"
                                placeholder="Ulangi password baru">

                            @error('new_password_confirmation')
                                <p class="text-error text-xs mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- BUTTON --}}
                        <div class="border-t border-base-200 pt-5">

                            <button
                                type="submit"
                                class="btn btn-primary rounded-xl
                                       px-6 gap-2">

                                <i class="fas fa-floppy-disk"></i>
                                Simpan Password Baru

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</x-layouts.app>
