<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SettingsController;
use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\Request;
use App\Models\HistoryLog;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $totalEquipment = Equipment::count();
        $activeIssuances = Issuance::whereNull('date_returned')->count();
        $pendingRequests = Request::where('status', 'pending')->count();
        $issuances = Issuance::with('equipment')
            ->when(request('department'), fn($query) => $query->where('department', request('department')))
            ->when(request('sort'), fn($query) => $query->orderBy(request('sort'), request('direction', 'asc')))
            ->get();
        return view('dashboard', compact('totalEquipment', 'activeIssuances', 'pendingRequests', 'issuances'));
    })->name('dashboard');

    Route::get('/inventory/issue', [InventoryController::class, 'create']);
    Route::post('/inventory/issue', [InventoryController::class, 'issue'])->name('inventory.issue');
    Route::post('/inventory/return', [InventoryController::class, 'return'])->name('inventory.return');
    Route::delete('/inventory/{id}', [InventoryController::class, 'delete'])->name('inventory.delete');

    Route::get('/staff', [StaffController::class, 'index'])->name('staff');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::patch('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

    Route::get('/history', fn() => view('history', ['history_logs' => HistoryLog::orderBy('action_date', 'desc')->get()]))->name('history');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';