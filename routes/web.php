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
    Route::get('/dashboard', function () {
        $totalEquipment = Equipment::count();
        $activeIssuances = Issuance::whereNull('date_returned')->count();
        $issuances = Issuance::with('equipment')
            ->when(request('department'), fn($query) => $query->whereHas('equipment', fn($q) => $q->where('department_id', request('department'))))
            ->when(request('sort'), fn($query) => $query->orderBy(request('sort'), request('direction', 'asc')))
            ->get();
        return view('dashboard', compact('totalEquipment', 'activeIssuances', 'issuances'));
    })->name('dashboard');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/issue', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory/issue', [InventoryController::class, 'issue'])->name('inventory.issue');
    Route::post('/inventory/check-duplicates', [InventoryController::class, 'checkDuplicates'])->name('inventory.check-duplicates');
    Route::post('/inventory/return/{issuance}', [InventoryController::class, 'return'])->name('inventory.return');
    Route::delete('/inventory/delete/{equipment}', [InventoryController::class, 'delete'])->name('inventory.delete');

    Route::get('/staff', [StaffController::class, 'index'])->name('staff');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::patch('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

    Route::get('/history', fn() => view('history', ['history_logs' => HistoryLog::orderBy('action_date', 'desc')->get()]))->name('history');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/department', [SettingsController::class, 'storeDepartment'])->name('settings.department.store');
    Route::patch('/settings/department/{department}', [SettingsController::class, 'updateDepartment'])->name('settings.department.update');
    Route::delete('/settings/department/{department}', [SettingsController::class, 'destroyDepartment'])->name('settings.department.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('settings');
    Route::patch('/', [SettingsController::class, 'update'])->name('settings.update');
    
    // Department routes
    Route::post('/department', [SettingsController::class, 'storeDepartment'])->name('settings.department.store');
    Route::patch('/department/{department}', [SettingsController::class, 'updateDepartment'])->name('settings.department.update');
    Route::delete('/department/{department}', [SettingsController::class, 'destroyDepartment'])->name('settings.department.destroy');
});

});

require __DIR__.'/auth.php';