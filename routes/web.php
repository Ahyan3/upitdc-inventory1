<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Request;
use App\Models\Settings;
use App\Models\HistoryLog;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inventory Routes
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventory');
        Route::get('/issue', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/issue', [InventoryController::class, 'issue'])->name('inventory.issue');
        Route::post('/check-duplicates', [InventoryController::class, 'checkDuplicates'])->name('inventory.check-duplicates');
        Route::post('/return/{issuance}', [InventoryController::class, 'return'])->name('inventory.return');
        Route::delete('/{equipment}', [InventoryController::class, 'delete'])->name('inventory.delete');
    });

    // Staff Routes
    Route::prefix('staff')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('staff');
        Route::post('/', [StaffController::class, 'store'])->name('staff.store');
        Route::patch('/{staff}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });

    // History Route
    Route::get('/history', function () {
        return view('history', [
            'history_logs' => \App\Models\HistoryLog::orderBy('action_date', 'desc')->get()
        ]);
    })->name('history');

    // Settings Routes
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings');
        Route::patch('/', [SettingsController::class, 'update'])->name('settings.update');
        
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

require __DIR__.'/auth.php';