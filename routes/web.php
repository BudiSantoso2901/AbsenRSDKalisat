<?php

use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
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

// Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login-prosess', [LoginController::class, 'login'])->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/admin/dashboard', [LoginController::class, 'dashboard'])->name('admin.dashboard');
Route::get('/pegawai/register', [LoginController::class, 'get_register'])->name('pegawai.register.form');
Route::post('/pegawai/register', [LoginController::class, 'register'])->name('pegawai.register');
Route::get('/pegawai/kamera', [AbsensiController::class, 'kamera'])->name('pegawai.kamera');
Route::prefix('pegawai')->group(function () {
    Route::get('/list', [PegawaiController::class, 'index'])->name('pegawai.index');
    Route::post('/tambah', [PegawaiController::class, 'store'])->name('pegawai.store');
    Route::put('/edit/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
    Route::delete('/hapus/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
});
