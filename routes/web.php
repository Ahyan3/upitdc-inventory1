<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/history', [HistoryController::class, 'history'])->name('history');

    // Inventory Routes
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventory');
        Route::get('/issue', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/issue', [InventoryController::class, 'issue'])->name('inventory.issue');
        // Route::get('/inventory/check-duplicates', [InventoryController::class, 'checkDuplicates'])->name('inventory.check-duplicates');
        Route::post('/inventory/check-duplicates', [InventoryController::class, 'checkDuplicates'])->name('inventory.check-duplicates');
        Route::post('/return/{issuance}', [InventoryController::class, 'return'])->name('inventory.return');
        Route::delete('/{equipment}', [InventoryController::class, 'delete'])->name('inventory.delete');
        Route::get('inventory/{equipment}', [InventoryController::class, 'view'])->name('inventory.view');
        Route::get('inventory/{equipment}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('inventory/{equipment}', [InventoryController::class, 'update'])->name('inventory.update');
    });

    // Staff Routes
    Route::prefix('staff')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/', [StaffController::class, 'store'])->name('staff.store');

        // Single staff routes
        Route::prefix('{staff}')->group(function () {
            Route::get('/edit', [StaffController::class, 'edit'])->name('staff.edit');
            Route::put('/', [StaffController::class, 'update'])->name('staff.update');
            Route::get('/history-logs', [StaffController::class, 'historyLogs'])->name('staff.history-logs');
            Route::put('/status', [StaffController::class, 'updateStatus'])->name('staff.status');
            Route::delete('/', [StaffController::class, 'destroy'])->name('staff.destroy');
        });
    });

    // History Route
    Route::get('/history', function () {
        return view('history', [
            'history_logs' => \App\Models\HistoryLog::orderBy('created_at', 'desc')->get()
        ]);
    })->name('history');
    Route::get('/history', [HistoryController::class, 'history'])->name('history');

    // Settings Routes
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings');
        Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Department Routes
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

// routes/web.php
Route::get('/test-inventory', function () {
    return 'Test inventory route works!';
})->name('test.inventory');

require __DIR__ . '/auth.php';
