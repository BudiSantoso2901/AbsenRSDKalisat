<?php

use App\Http\Controllers\MobilCinta\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\MobilCinta\JourneyHistoryController;
use App\Http\Controllers\MobilCinta\Auth\AuthController;
use App\Http\Controllers\MobilCinta\Admin\AccountController;
use App\Http\Controllers\MobilCinta\Admin\VehicleController;
use App\Http\Controllers\MobilCinta\Koordinator\DashboardController as KoordinatorDashboardController;
use App\Http\Controllers\MobilCinta\Koordinator\JourneyController as KoordinatorJourneyController;
use App\Http\Controllers\MobilCinta\Perawat\DashboardController as PerawatDashboardController;
use App\Http\Controllers\MobilCinta\Perawat\JourneyController as PerawatJourneyController;
use App\Http\Controllers\MobilCinta\Sopir\DashboardController as SopirDashboardController;
use App\Http\Controllers\MobilCinta\Sopir\JourneyController as SopirJourneyController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobil-cinta')->name('mobil-cinta.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['mobil-cinta.auth', 'mobil-cinta.role:admin'])->group(function () {
        Route::get('/admin/dashboard', AdminDashboardController::class)->name('admin.dashboard');
        // Export laporan perjalanan ke Excel-compatible CSV
        Route::get('/admin/dashboard/export/excel', [AdminDashboardController::class, 'exportExcel'])->name('admin.dashboard.export.excel');
        // Halaman cetak / simpan PDF
        Route::get('/admin/dashboard/export/pdf', [AdminDashboardController::class, 'exportPdf'])->name('admin.dashboard.export.pdf');
        Route::get('/admin/riwayat', [JourneyHistoryController::class, 'index'])->name('admin.riwayat');

        Route::get('/admin/akun', [AccountController::class, 'index'])
            ->name('admin.akun.index');

        // Endpoint autocomplete diletakkan di atas route akun dinamis.
        Route::get('/admin/akun/suggest', [AccountController::class, 'suggestions'])
            ->name('admin.akun.suggest');

        Route::get('/admin/akun/create', [AccountController::class, 'create'])
            ->name('admin.akun.create');

        Route::post('/admin/akun', [AccountController::class, 'store'])
            ->name('admin.akun.store');

        Route::get('/admin/akun/{account}/edit', [AccountController::class, 'edit'])
            ->name('admin.akun.edit');

        Route::put('/admin/akun/{account}', [AccountController::class, 'update'])
            ->name('admin.akun.update');

        Route::patch('/admin/akun/{account}/status', [AccountController::class, 'toggleStatus'])
            ->name('admin.akun.toggle-status');

        Route::patch('/admin/akun/{account}/reset-password', [AccountController::class, 'resetPassword'])
            ->name('admin.akun.reset-password');

        Route::get('/admin/kendaraan', [VehicleController::class, 'index'])
            ->name('admin.kendaraan.index');

        Route::post('/admin/kendaraan', [VehicleController::class, 'store'])
            ->name('admin.kendaraan.store');

        Route::put('/admin/kendaraan/{vehicle}', [VehicleController::class, 'update'])
            ->name('admin.kendaraan.update');

        Route::delete('/admin/kendaraan/{vehicle}', [VehicleController::class, 'destroy'])
            ->name('admin.kendaraan.destroy');
    });

    Route::middleware(['mobil-cinta.auth', 'mobil-cinta.role:koordinator'])->group(function () {
        Route::get('/koordinator/dashboard', KoordinatorDashboardController::class)->name('koordinator.dashboard');
        Route::get('/koordinator/riwayat', [JourneyHistoryController::class, 'index'])->name('koordinator.riwayat');
        Route::post('/koordinator/perjalanan/{journey}/penugasan', [KoordinatorJourneyController::class, 'assign'])->name('koordinator.perjalanan.assign');
        Route::post('/koordinator/perjalanan/{journey}/tolak', [KoordinatorJourneyController::class, 'reject'])->name('koordinator.perjalanan.reject');
    });

    Route::middleware(['mobil-cinta.auth', 'mobil-cinta.role:perawat'])->group(function () {
        Route::get('/perawat/dashboard', PerawatDashboardController::class)->name('perawat.dashboard');
        Route::get('/perawat/riwayat', [JourneyHistoryController::class, 'index'])->name('perawat.riwayat');
        Route::post('/perawat/perjalanan', [PerawatJourneyController::class, 'store'])->name('perawat.perjalanan.store');
        Route::get('/perawat/perjalanan/{journey}/edit', [PerawatJourneyController::class, 'edit'])->name('perawat.perjalanan.edit');
        Route::put('/perawat/perjalanan/{journey}', [PerawatJourneyController::class, 'update'])->name('perawat.perjalanan.update');
        Route::post('/perawat/perjalanan/{journey}/batalkan', [PerawatJourneyController::class, 'cancel'])->name('perawat.perjalanan.cancel');
    });

    Route::middleware(['mobil-cinta.auth', 'mobil-cinta.role:sopir'])->group(function () {
        Route::get('/sopir/dashboard', SopirDashboardController::class)->name('sopir.dashboard');
        Route::get('/sopir/riwayat', [JourneyHistoryController::class, 'index'])->name('sopir.riwayat');
        Route::post('/sopir/perjalanan/{journey}/berangkat', [SopirJourneyController::class, 'depart'])->name('sopir.perjalanan.depart');
        Route::post('/sopir/perjalanan/{journey}/sampai', [SopirJourneyController::class, 'arrive'])->name('sopir.perjalanan.arrive');
    });
});
