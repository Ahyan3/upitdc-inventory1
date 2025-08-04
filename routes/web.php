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
        Route::get('/{id}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::get('/{id}/details', [InventoryController::class, 'details'])->name('inventory.details');
        // Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        // Route::put('/{id}', [InventoryController::class, 'update'])->name('inventory.update');
        // Route::put('/{id}/edit', [InventoryController::class, 'show'])->name('inventory.edit.form'); 
        Route::put('/{id}/update', [InventoryController::class, 'apiUpdate'])->name('inventory.apiUpdate');
        Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
        Route::get('/export/csv', [InventoryController::class, 'exportCsv'])->name('inventory.export');
        Route::get('/chart-data', [InventoryController::class, 'chartData'])->name('inventory.chart-data');
        Route::delete('/{equipment}', [InventoryController::class, 'delete'])->name('inventory.delete');
            // Route::get('inventory/{equipment}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::get('/inventory/history', [InventoryController::class, 'historyLogs'])->name('inventory.history');
        Route::post('/inventory/update-status', [InventoryController::class, 'updateStatus'])->name('inventory.update-status');
        Route::post('/inventory/issue-out', [InventoryController::class, 'issueOut'])->name('inventory.issue-out');
        Route::post('/return/{issuance}', [InventoryController::class, 'return'])->name('inventory.return');
    });

    Route::prefix('')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');

        // Put specific routes BEFORE generic {id} routes
        Route::get('/staff/export-csv', [StaffController::class, 'exportCsv'])->name('staff.export-csv');
        Route::get('/total-staff', [StaffController::class, 'total'])->name('staff.total');
        Route::get('/active', [StaffController::class, 'getActiveStaff'])->name('staff.active');
        Route::get('/inactive', [StaffController::class, 'getInactiveStaff'])->name('staff.inactive');
        Route::post('/validate', [StaffController::class, 'validateStaff'])->name('staff.validate');
        Route::post('/check-email', [StaffController::class, 'checkEmail'])->name('staff.check-email');
        Route::post('/check-username', [StaffController::class, 'checkUsername'])->name('staff.check-username');
        Route::get('/staff/{staff}/export-history-logs', [App\Http\Controllers\StaffController::class, 'exportHistoryLogs'])->name('staff.export-history-logs');


        // Staff-specific routes with full path
        Route::get('/staff/{staff}/history-logs', [StaffController::class, 'historyLogs'])->name('staff.history-logs');

        // Debug routes (remove these after debugging)
        Route::get('/debug/history-logs/{id}', [StaffController::class, 'debugHistoryLogs'])->name('staff.debug-history');
        Route::get('/debug/create-test-log/{id}', [StaffController::class, 'createTestLog'])->name('staff.create-test-log');

        // Generic {id} routes LAST to avoid conflicts
        Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
        Route::put('/staff/{staff}/status', [StaffController::class, 'updateStatus'])->name('staff.status');
    });

    // History Route
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/export/csv', [HistoryController::class, 'exportHistoryCsv'])->name('history.export.csv');



    // Issuance Routes
    Route::get('/issuances/{id}', [IssuanceController::class, 'show'])->name('issuances.show');
    Route::post('/issuances', [IssuanceController::class, 'store'])->name('issuances.store');
    Route::put('/issuances/{id}', [IssuanceController::class, 'update'])->name('issuances.update');
    Route::delete('/issuances/{id}', [IssuanceController::class, 'destroy'])->name('issuances.destroy');
    Route::patch('/issuances/{id}/status', [IssuanceController::class, 'updateStatus'])->name('issuances.status');
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/export/csv', [HistoryController::class, 'exportHistoryCsv'])->name('history.export.csv');
    Route::get('/history/inventory/export/csv', [HistoryController::class, 'exportInventoryCsv'])->name('history.inventory.export.csv');

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