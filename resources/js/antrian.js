
// Jangan membuka koneksi khusus antrean
// jika halaman tidak menampilkan data antrean.
const elemenAntrean = document.querySelectorAll(
    '[data-antrian-jadwal]'
);

if (elemenAntrean.length > 0 && window.Echo) {

    window.Echo
        .channel('antrian-poliklinik')
        .listen('.antrian.dipanggil', (event) => {

            const jadwalId = Number(event.jadwal_id);

            const nomor =
                event.no_antrian_dilayani === null
                    ? '—'
                    : String(event.no_antrian_dilayani);

            // Perbarui nomor pada banner dan seluruh tabel.
            elemenAntrean.forEach((elemen) => {

                const idPadaHalaman = Number(
                    elemen.dataset.antrianJadwal
                );

                if (idPadaHalaman === jadwalId) {
                    elemen.textContent = nomor;
                }
            });

            // Perbarui keterangan pada banner antrean aktif.
            const banner = document.getElementById(
                'keterangan-antrian-aktif'
            );

            const nomorBanner = document.querySelector(
                '[data-antrian-jadwal][data-antrean-aktif]'
            );

            if (
                banner &&
                nomorBanner &&
                Number(nomorBanner.dataset.antrianJadwal)
                    === jadwalId
            ) {
                banner.classList.toggle(
                    'hidden',
                    event.no_antrian_dilayani !== null
                );
            }
        });
}
