<?php

use Illuminate\Support\Facades\Route;

// Authentication Controller
use App\Http\Controllers\AuthController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminPoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\Admin\ObatController;

// Dokter Controllers
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use App\Http\Controllers\Dokter\PeriksaPasienController;
use App\Http\Controllers\Dokter\RiwayatPasienController;

// Pasien Controllers
use App\Http\Controllers\Pasien\PoliController as PasienPoliController;

use App\Http\Controllers\Pasien\DashboardController as PasienDashboardController;

use App\Http\Controllers\Pasien\RiwayatPendaftaranController;

use App\Http\Controllers\Dokter\PanggilAntrianController;


use App\Http\Controllers\Pasien\PembayaranController as PasienPembayaranController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;

use App\Http\Controllers\NotifikasiController;

use App\Http\Controllers\ExportExcelController;

use App\Http\Controllers\ProfileController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/login', [
    AuthController::class,
    'showLogin'
])->name('login');

Route::post('/login', [
    AuthController::class,
    'login'
]);

Route::get('/register', [
    AuthController::class,
    'showRegister'
]);

Route::post('/register', [
    AuthController::class,
    'register'
])->name('register');

Route::post('/logout', [
    AuthController::class,
    'logout'
])->name('logout');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        // Dashboard Admin
        Route::view('/dashboard', 'admin.dashboard')
            ->name('admin.dashboard');

        // CRUD Poli
        Route::resource(
            'polis',
            AdminPoliController::class
        );

        // CRUD Dokter
        Route::resource(
            'dokter',
            DokterController::class
        );

        // CRUD Pasien
        Route::resource(
            'pasien',
            PasienController::class
        );

        // CRUD Obat
        Route::resource(
            'obat',
            ObatController::class
        )->except(['show']);


        // Halaman daftar pembayaran menunggu verifikasi
        Route::get(
            '/pembayaran',
            [AdminPembayaranController::class, 'index']
        )->name('admin.pembayaran.index');

        // Menampilkan foto bukti melalui akses admin
        Route::get(
            '/pembayaran/{id}/bukti',
            [AdminPembayaranController::class, 'bukti']
        )->whereNumber('id')->name('admin.pembayaran.bukti');

        // Konfirmasi pembayaran menjadi lunas
        Route::post(
            '/pembayaran/{id}/verifikasi',
            [AdminPembayaranController::class, 'verifikasi']
        )->whereNumber('id')->name('admin.pembayaran.verifikasi');


        // ======================================
        // EXPORT EXCEL ADMIN
        // ======================================
    
        Route::get(
            '/export/dokter',
            [ExportExcelController::class, 'dokter']
        )->name('export.admin.dokter');

        Route::get(
            '/export/pasien',
            [ExportExcelController::class, 'pasien']
        )->name('export.admin.pasien');

        Route::get(
            '/export/obat',
            [ExportExcelController::class, 'obat']
        )->name('export.admin.obat');


    });


/*
|--------------------------------------------------------------------------
| DOKTER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:dokter'])
    ->prefix('dokter')
    ->group(function () {

        // Dashboard Dokter
        Route::view('/dashboard', 'dokter.dashboard')
            ->name('dokter.dashboard');

        // CRUD Jadwal Periksa
        Route::resource(
            'jadwal-periksa',
            JadwalPeriksaController::class
        )
            ->parameters([
                'jadwal-periksa' => 'jadwalPeriksa',
            ])
            ->except(['show']);

        // Daftar Pasien yang Akan Diperiksa
        Route::get(
            '/periksa-pasien',
            [PeriksaPasienController::class, 'index']
        )->name('periksa-pasien.index');

        // Form Pemeriksaan Pasien
        Route::get(
            '/periksa-pasien/{id}',
            [PeriksaPasienController::class, 'create']
        )
            ->whereNumber('id')
            ->name('periksa-pasien.create');

        // Simpan Pemeriksaan Pasien
        Route::post(
            '/periksa-pasien',
            [PeriksaPasienController::class, 'store']
        )->name('periksa-pasien.store');

        // Riwayat Pemeriksaan Pasien
        Route::get(
            '/riwayat-pasien',
            [RiwayatPasienController::class, 'index']
        )->name('riwayat-pasien.index');

        // Detail Riwayat Pemeriksaan
        Route::get(
            '/riwayat-pasien/{id}',
            [RiwayatPasienController::class, 'show']
        )
            ->whereNumber('id')
            ->name('riwayat-pasien.show');

        // Memanggil nomor antrean pasien
        Route::post(
            '/periksa-pasien/{id}/panggil',
            PanggilAntrianController::class
        )
            ->whereNumber('id')
            ->name('periksa-pasien.panggil');


        // ======================================
        // EXPORT EXCEL DOKTER
        // ======================================
    
        Route::get(
            '/export/jadwal-periksa',
            [ExportExcelController::class, 'jadwalPeriksa']
        )->name('export.dokter.jadwal');

        Route::get(
            '/export/riwayat-pasien',
            [ExportExcelController::class, 'riwayatPasien']
        )->name('export.dokter.riwayat');


    });


/*
|--------------------------------------------------------------------------
| PASIEN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:pasien'])
    ->prefix('pasien')
    ->group(function () {

        // Dashboard Pasien
        Route::get('/dashboard', [PasienDashboardController::class, 'index'])
            ->name('pasien.dashboard');

        // Form Pendaftaran Poli
        Route::get(
            '/daftar',
            [PasienPoliController::class, 'get']
        )->name('pasien.daftar');

        // Simpan Pendaftaran Poli
        Route::post(
            '/daftar',
            [PasienPoliController::class, 'submit']
        )->name('pasien.daftar.submit');

        // Riwayat Pendaftaran Poli Pasien
        Route::get(
            '/riwayat-pendaftaran',
            [RiwayatPendaftaranController::class, 'index']
        )->name('pasien.riwayat.index');

        // Detail Riwayat Pendaftaran
        Route::get(
            '/riwayat-pendaftaran/{id}',
            [RiwayatPendaftaranController::class, 'show']
        )->whereNumber('id')->name('pasien.riwayat.show');


        // Halaman tagihan dan upload bukti pembayaran
        Route::get(
            '/pembayaran',
            [PasienPembayaranController::class, 'index']
        )->name('pasien.pembayaran.index');

        Route::post(
            '/pembayaran/{id}/upload',
            [PasienPembayaranController::class, 'store']
        )->whereNumber('id')->name('pasien.pembayaran.store');


    });


// ===========================================
// NOTIFIKASI - SEMUA ROLE YANG SUDAH LOGIN
// ===========================================

Route::middleware('auth')->group(function () {

    // Halaman daftar notifikasi
    Route::get(
        '/notifikasi',
        [NotifikasiController::class, 'index']
    )->name('notifikasi.index');

    // Menandai satu notifikasi sebagai dibaca
    Route::post(
        '/notifikasi/{id}/baca',
        [NotifikasiController::class, 'baca']
    )->name('notifikasi.baca');



    // ==========================================
    // PROFILE & GANTI PASSWORD - SEMUA ROLE
    // ==========================================

        // Halaman profil
        Route::get(
            '/profil',
            [ProfileController::class, 'index']
        )->name('profil.index');

        // Proses perubahan password
        Route::patch(
            '/profil/password',
            [ProfileController::class, 'updatePassword']
        )->name('profil.password.update');

});

