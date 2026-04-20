<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JamKerjaController;
use App\Http\Controllers\KontenAbsenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\RuanganController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view(view: 'Auth.login');
// });

Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login-prosess', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/pegawai/register', [LoginController::class, 'get_register'])->name('pegawai.register.form');
Route::post('/pegawai/register', [LoginController::class, 'register'])->name('pegawai.register');

Route::prefix('/')
    ->middleware('admin.auth')
    ->group(function () {
        Route::get('dashboard', [LoginController::class, 'dashboard'])->name('admin.dashboard');
        Route::prefix('pegawai')->group(function () {
            Route::get('/list', [PegawaiController::class, 'index'])->name('pegawai.index');
            Route::post('/tambah', [PegawaiController::class, 'store'])->name('pegawai.store');
            Route::put('/edit/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
            Route::delete('/hapus/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
        });
        Route::prefix('ruangan')->group(function () {
            Route::get('/', [RuanganController::class, 'index'])->name('ruangan.index');
            Route::post('/store', [RuanganController::class, 'store'])->name('ruangan.store');
            Route::put('/update/{id}', [RuanganController::class, 'update']);
            Route::delete('/delete/{id}', [RuanganController::class, 'destroy']);
        });
        Route::prefix('konten')->group(function () {
            Route::get('/', [KontenAbsenController::class, 'view_konten_admin'])->name('admin.konten');
            Route::post('/admin/konten/valid', [KontenAbsenController::class, 'valid'])->name('admin.konten.valid');
            Route::post('/admin/konten/tolak', [KontenAbsenController::class, 'tolak'])->name('admin.konten.tolak');
        });
        Route::prefix('jabatan')->group(function () {
            Route::get('/list', [JabatanController::class, 'index'])->name('jabatan.index');
            Route::post('/tambah', [JabatanController::class, 'store'])->name('jabatan.store');
            Route::put('/edit/{id}', [JabatanController::class, 'update'])->name('jabatan.update');
            Route::delete('/hapus/{id}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');
        });

        Route::prefix('jam-kerja')->group(function () {
            Route::get('/list', [JamKerjaController::class, 'index'])->name('jam-kerja.index');
            Route::post('/tambah', [JamKerjaController::class, 'store'])->name('jam-kerja.store');
            Route::put('/edit/{id}', [JamKerjaController::class, 'update'])->name('jam-kerja.update');
            Route::delete('/hapus/{id}', [JamKerjaController::class, 'destroy'])->name('jam-kerja.destroy');
        });

        Route::prefix('lokasi')->group(function () {
            Route::get('/list', [LokasiController::class, 'index'])->name('lokasi.index');
            Route::post('/tambah', [LokasiController::class, 'store'])->name('lokasi.store');
            Route::put('/edit/{id}', [LokasiController::class, 'update'])->name('lokasi.update');
            Route::delete('/hapus/{id}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');
        });

        Route::prefix('absensi')->group(function () {
            Route::get('/', [AbsensiController::class, 'index'])->name('absensi.index');
            Route::get('/data-absen', [AbsensiController::class, 'absen_get'])->name('admin.absensi.index');
            Route::get('/detail/{pegawai}', [AbsensiController::class, 'detail'])->name('absensi.detail');
            Route::put('edit/{id}', [AbsensiController::class, 'update'])->name('absensi.inline-update');
            Route::get('export/pdf/{pegawai}', [AbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');
            Route::get('export-filter/pdf', [AbsensiController::class, 'export_Pdf'])->name('absensi.exportAll.pdf');
            Route::get('/histori-absensi', [LoginController::class, 'histori'])->name('absensi.histori');
        });
        Route::prefix('adm')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('user.index');
            Route::post('/store', [UserController::class, 'store'])->name('user.store');
            Route::get('/{id}', [UserController::class, 'show'])->name('user.show');
            Route::put('/update/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');
        });
    });
Route::prefix('pegawai')
    ->middleware(['pegawai.auth'])
    ->group(function () {

        Route::post('/absensi', [AbsensiController::class, 'absen'])
            ->name('absensi.store');
        Route::get('/kamera', [AbsensiController::class, 'kamera'])->name('pegawai.kamera');
        Route::get('/dashboard', [PegawaiController::class, 'dashboard_pegawai'])->name('pegawai.dashboard');
        Route::post('/update-shift', [PegawaiController::class, 'updateShift'])
            ->name('pegawai.updateShift');
        Route::get('/panduan', [PegawaiController::class, 'panduan'])->name('pegawai.panduan');
        Route::post('/absensi-kegiatan', [AbsensiController::class, 'absenKegiatan'])
            ->name('absensi.kegiatan');
        Route::get('/absen-konten', [KontenAbsenController::class, 'view_konten_absen'])
            ->name('pegawai.konten.index');

        Route::get('/absen-konten/create', [KontenAbsenController::class, 'create_konten_absen'])
            ->name('pegawai.konten.create');

        Route::post('/absen-konten/store', [KontenAbsenController::class, 'store_konten_absen'])
            ->name('pegawai.konten.store');
        Route::post('/pegawai/konten/update', [KontenAbsenController::class, 'update_konten'])
            ->name('pegawai.konten.update');
    });
