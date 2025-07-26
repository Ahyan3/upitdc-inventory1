<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\IssuanceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard-counts', [DashboardController::class, 'getCounts'])->name('dashboard.counts');
    Route::get('/dashboard/export-csv', [DashboardController::class, 'exportCsv'])->name('dashboard.export.csv');

    // Inventory Routes
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventory');
        Route::get('/issue', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/issue', [InventoryController::class, 'issue'])->name('inventory.issue');
        Route::post('/check-duplicates', [InventoryController::class, 'checkDuplicates'])->name('inventory.check-duplicates');
        Route::post('/return/{issuance}', [InventoryController::class, 'return'])->name('inventory.return');
        Route::get('/{id}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/{id}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
        Route::get('/export/csv', [InventoryController::class, 'exportCsv'])->name('inventory.export.csv');
        Route::get('/chart-data', [InventoryController::class, 'chartData'])->name('inventory.chart-data');
    });

    // Staff Routes
    Route::prefix('staff')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/total-staff', [StaffController::class, 'total']);
        Route::get('/export-csv', [StaffController::class, 'exportCsv'])->name('staff.export-csv');
        Route::prefix('{staff}')->group(function () {
            Route::get('/edit', [StaffController::class, 'edit'])->name('staff.edit');
            Route::put('/', [StaffController::class, 'update'])->name('staff.update');
            Route::get('/history-logs', [StaffController::class, 'historyLogs'])->name('staff.history-logs');
            Route::put('/status', [StaffController::class, 'updateStatus'])->name('staff.status');
            Route::delete('/', [StaffController::class, 'destroy'])->name('staff.destroy');
            Route::get('/api/total-staff', [StaffController::class, 'totalStaff'])->name('staff.total');
        });
    });

    // History Route
    Route::get('/history', [HistoryController::class, 'index'])->name('history');

    // Issuance Routes
    Route::get('/issuances/{id}', [IssuanceController::class, 'show'])->name('issuances.show');
    Route::post('/issuances', [IssuanceController::class, 'store'])->name('issuances.store');
    Route::put('/issuances/{id}', [IssuanceController::class, 'update'])->name('issuances.update');
    Route::delete('/issuances/{id}', [IssuanceController::class, 'destroy'])->name('issuances.destroy');
    Route::patch('/issuances/{id}/status', [IssuanceController::class, 'updateStatus'])->name('issuances.status');

    // Settings Routes
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings');
        Route::patch('/', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('/department', [SettingsController::class, 'storeDepartment'])->name('settings.department.store');
        Route::patch('/department/{department}', [SettingsController::class, 'updateDepartment'])->name('settings.department.update');
        Route::delete('/department/{department}', [SettingsController::class, 'destroyDepartment'])->name('settings.department.destroy');
    });

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__ . '/auth.php';
    // Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::delete('/{equipment}', [InventoryController::class, 'delete'])->name('inventory.delete');
    Route::get('inventory/{equipment}', [InventoryController::class, 'show'])->name('inventory.show');