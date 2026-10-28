
<x-layouts.app title="Periksa Pasien">

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('periksa-pasien.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-lg
                  bg-slate-100 hover:bg-slate-200
                  text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Periksa Pasien
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Isi hasil pemeriksaan dan obat yang diberikan kepada pasien.
            </p>
        </div>
    </div>

    {{-- ================= CARD FORM ================= --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-6 md:p-8">

            <form action="{{ route('periksa-pasien.store') }}"
                  method="POST"
                  id="formPeriksa"
                  novalidate>

                @csrf

                {{-- ID Pendaftaran Poli --}}
                <input type="hidden"
                       name="id_daftar_poli"
                       value="{{ $id }}">

                {{-- Data obat yang akan dikirim ke controller --}}
                <input type="hidden"
                       name="obat_json"
                       id="obat_json"
                       value="{{ old('obat_json', '[]') }}">

                {{-- Total harga obat --}}
                <input type="hidden"
                       name="biaya_periksa"
                       id="biaya_periksa"
                       value="{{ old('biaya_periksa', 0) }}">

                {{-- ================= PILIH OBAT ================= --}}
                <div class="mb-6">
                    <label for="pilih_obat"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Pilih Obat <span class="text-red-500">*</span>
                    </label>

                    <select id="pilih_obat"
                            class="select select-bordered border-2 rounded-lg
                                   w-full focus:outline-none focus:ring-2
                                   focus:ring-primary">

                        <option value="">-- Pilih Obat --</option>

                        @foreach($obats as $obat)
                            <option value="{{ $obat->id }}"
                                    data-nama="{{ $obat->nama_obat }}"
                                    data-harga="{{ $obat->harga }}"
                                    data-kemasan="{{ $obat->kemasan }}">

                                {{ $obat->nama_obat }}
                                - {{ $obat->kemasan }}
                                (Rp {{ number_format((float) $obat->harga, 0, ',', '.') }})

                            </option>
                        @endforeach

                    </select>

                    @error('obat_json')
                        <p class="text-xs text-red-500 mt-2">
                            {{ $message }}
                        </p>
                    @enderror

                    <p id="obatError"
                       class="hidden text-xs text-red-500 mt-2">
                        Pilih minimal satu obat sebelum menyimpan pemeriksaan.
                    </p>
                </div>

                {{-- ================= OBAT TERPILIH ================= --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-semibold text-slate-700">
                            Obat Terpilih
                        </label>

                        <span id="jumlahObat"
                              class="text-xs font-semibold px-3 py-1 rounded-full
                                     bg-blue-100 text-blue-700">
                            0 Obat
                        </span>
                    </div>

                    {{-- Daftar obat terpilih --}}
                    <div id="daftarObat"
                         class="space-y-2">

                        <div id="obatKosong"
                             class="border-2 border-dashed border-slate-200
                                    rounded-xl py-8 text-center text-slate-400">

                            <i class="fas fa-capsules text-2xl mb-2 block"></i>

                            <p class="text-sm">
                                Belum ada obat yang dipilih.
                            </p>

                        </div>

                    </div>
                </div>

                {{-- ================= TOTAL HARGA ================= --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Total Harga
                    </label>

                    <div class="flex items-center justify-between
                                bg-slate-50 border border-slate-200
                                rounded-xl px-5 py-4">

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-green-100
                                        text-green-600 flex items-center
                                        justify-center">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>

                            <div>
                                <p class="text-xs text-slate-500">
                                    Total Harga Obat
                                </p>

                                <p id="totalHarga"
                                   class="text-xl font-bold text-slate-800">
                                    Rp 0
                                </p>
                            </div>
                        </div>

                        <span class="text-xs font-semibold text-green-700
                                     bg-green-100 px-3 py-1 rounded-full">
                            Otomatis
                        </span>

                    </div>

                    <p class="text-xs text-slate-500 mt-2">
                        Biaya pemeriksaan sebesar Rp 150.000 akan
                        ditambahkan saat data disimpan.
                    </p>
                </div>

                {{-- ================= CATATAN ================= --}}
                <div class="mb-8">
                    <label for="catatan"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Catatan
                        <span class="text-xs font-normal text-slate-400">
                            (Opsional)
                        </span>
                    </label>

                    <textarea name="catatan"
                              id="catatan"
                              rows="4"
                              placeholder="Masukkan catatan hasil pemeriksaan pasien..."
                              class="textarea textarea-bordered border-2
                                     rounded-lg p-3 w-full
                                     focus:outline-none focus:ring-2
                                     focus:ring-primary
                                     @error('catatan') textarea-error @enderror">{{ old('catatan') }}</textarea>

                    @error('catatan')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- ================= BUTTONS ================= --}}
                <div class="flex items-center gap-3">

                    <button type="submit"
                            id="btnSimpan"
                            class="inline-flex items-center gap-2 px-6 py-2.5
                                   bg-primary hover:bg-primary/90
                                   text-white rounded-lg font-semibold
                                   text-sm transition">

                        <i class="fas fa-save text-sm"></i>
                        Simpan

                    </button>

                    <a href="{{ route('periksa-pasien.index') }}"
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

    {{-- ================= JAVASCRIPT ================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Element HTML
            const pilihObat = document.getElementById('pilih_obat');
            const daftarObat = document.getElementById('daftarObat');
            const obatJsonInput = document.getElementById('obat_json');
            const biayaPeriksaInput = document.getElementById('biaya_periksa');
            const totalHarga = document.getElementById('totalHarga');
            const jumlahObat = document.getElementById('jumlahObat');
            const obatError = document.getElementById('obatError');
            const formPeriksa = document.getElementById('formPeriksa');

            // Array untuk menyimpan ID obat terpilih
            let obatTerpilih = [];

            // Format angka menjadi mata uang Rupiah
            const formatRupiah = (angka) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(angka);
            };

            // Ambil data obat dari option dropdown
            const ambilDataObat = (id) => {
                const option = Array.from(pilihObat.options)
                    .find(option => option.value === String(id));

                if (!option) {
                    return null;
                }

                return {
                    id: String(option.value),
                    nama: option.dataset.nama,
                    kemasan: option.dataset.kemasan,
                    harga: Number(option.dataset.harga)
                };
            };

            // Render daftar obat terpilih
            function renderObat() {
                daftarObat.innerHTML = '';

                let total = 0;

                // Jika belum ada obat
                if (obatTerpilih.length === 0) {
                    const empty = document.createElement('div');

                    empty.className =
                        'border-2 border-dashed border-slate-200 ' +
                        'rounded-xl py-8 text-center text-slate-400';

                    empty.innerHTML =
                        '<i class="fas fa-capsules text-2xl mb-2 block"></i>' +
                        '<p class="text-sm">Belum ada obat yang dipilih.</p>';

                    daftarObat.appendChild(empty);
                }

                // Tampilkan setiap obat
                obatTerpilih.forEach((id) => {
                    const obat = ambilDataObat(id);

                    if (!obat) {
                        return;
                    }

                    total += obat.harga;

                    const item = document.createElement('div');

                    item.className =
                        'flex flex-col sm:flex-row sm:items-center ' +
                        'sm:justify-between gap-3 p-4 bg-slate-50 ' +
                        'border border-slate-200 rounded-xl';

                    // Informasi obat
                    const informasi = document.createElement('div');
                    informasi.className = 'flex items-center gap-3';

                    const ikon = document.createElement('div');
                    ikon.className =
                        'w-10 h-10 rounded-lg bg-blue-100 text-blue-600 ' +
                        'flex items-center justify-center shrink-0';

                    ikon.innerHTML = '<i class="fas fa-pills"></i>';

                    const detail = document.createElement('div');

                    const nama = document.createElement('p');
                    nama.className = 'text-sm font-semibold text-slate-800';
                    nama.textContent = obat.nama;

                    const kemasan = document.createElement('p');
                    kemasan.className = 'text-xs text-slate-500 mt-1';
                    kemasan.textContent = obat.kemasan;

                    detail.appendChild(nama);
                    detail.appendChild(kemasan);

                    informasi.appendChild(ikon);
                    informasi.appendChild(detail);

                    // Harga dan tombol hapus
                    const aksi = document.createElement('div');
                    aksi.className = 'flex items-center gap-3';

                    const harga = document.createElement('span');
                    harga.className = 'text-sm font-semibold text-slate-700';
                    harga.textContent = formatRupiah(obat.harga);

                    const hapus = document.createElement('button');
                    hapus.type = 'button';
                    hapus.className =
                        'inline-flex items-center justify-center ' +
                        'w-9 h-9 rounded-lg bg-red-100 text-red-600 ' +
                        'hover:bg-red-200 transition';

                    hapus.innerHTML = '<i class="fas fa-trash text-xs"></i>';
                    hapus.title = 'Hapus obat';

                    hapus.addEventListener('click', function () {
                        hapusObat(obat.id);
                    });

                    aksi.appendChild(harga);
                    aksi.appendChild(hapus);

                    item.appendChild(informasi);
                    item.appendChild(aksi);

                    daftarObat.appendChild(item);
                });

                // Update total harga
                const totalBulat = Math.round(total);

                totalHarga.textContent = formatRupiah(totalBulat);
                biayaPeriksaInput.value = totalBulat;

                // Update jumlah obat
                jumlahObat.textContent = obatTerpilih.length + ' Obat';

                // Update JSON untuk dikirim ke controller
                obatJsonInput.value = JSON.stringify(obatTerpilih);

                // Hilangkan error setelah obat dipilih
                if (obatTerpilih.length > 0) {
                    obatError.classList.add('hidden');
                }
            }

            // Tambahkan obat dari dropdown
            pilihObat.addEventListener('change', function () {
                const id = this.value;

                if (!id) {
                    return;
                }

                const obat = ambilDataObat(id);

                if (!obat) {
                    return;
                }

                // Cegah obat yang sama ditambahkan dua kali
                if (obatTerpilih.includes(id)) {
                    alert('Obat ini sudah ditambahkan.');
                    this.value = '';
                    return;
                }

                obatTerpilih.push(id);

                renderObat();

                // Reset dropdown
                this.value = '';
            });

            // Hapus obat dari daftar
            function hapusObat(id) {
                obatTerpilih = obatTerpilih.filter(
                    obatId => obatId !== String(id)
                );

                renderObat();
            }

            // Kembalikan pilihan jika validasi server gagal
            try {
                const pilihanLama = JSON.parse(obatJsonInput.value || '[]');

                if (Array.isArray(pilihanLama)) {
                    obatTerpilih = [
                        ...new Set(
                            pilihanLama
                                .map(String)
                                .filter(id => ambilDataObat(id) !== null)
                        )
                    ];
                }
            } catch (error) {
                obatTerpilih = [];
            }

            // Render saat halaman pertama dibuka
            renderObat();

            // Validasi sebelum submit
            formPeriksa.addEventListener('submit', function (event) {

                if (obatTerpilih.length === 0) {
                    event.preventDefault();

                    obatError.classList.remove('hidden');
                    pilihObat.focus();

                    return;
                }

                // Pastikan data terbaru sudah dimasukkan
                renderObat();
            });

        });
    </script>

</x-layouts.app>
