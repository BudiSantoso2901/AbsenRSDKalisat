<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JamKerjaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PegawaiController;

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

Route::get('/', [LoginController::class, 'showLogin'])->name('login');
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
            Route::get('/detail/{pegawai}', [AbsensiController::class, 'detail'])->name('absensi.detail');
            Route::get('/data-absen', [AbsensiController::class, 'absen_get'])->name('admin.absensi.index');
            Route::put('edit/{id}', [AbsensiController::class, 'update'])->name('absensi.inline-update');
            Route::get('export/pdf/{pegawai}', [AbsensiController::class, 'exportPdf'])->name('absensi.export.pdf');
            Route::get('export-filter/pdf', [AbsensiController::class, 'export_Pdf'])->name('absensi.exportAll.pdf');
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
    });
