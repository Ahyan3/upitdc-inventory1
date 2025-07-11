<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\Request;
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
        $history = [
            ['description' => 'Laptop issued to John Doe on 2025-06-01'],
            ['description' => 'Monitor returned by Jane Smith on 2025-07-01'],
        ];

        return view('dashboard', compact('totalEquipment', 'activeIssuances', 'pendingRequests', 'issuances', 'history'));
    })->name('dashboard');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory/issue', [InventoryController::class, 'issue'])->name('inventory.issue');
    Route::post('/inventory/return', [InventoryController::class, 'return'])->name('inventory.return');
    Route::delete('/inventory/{id}', [InventoryController::class, 'delete'])->name('inventory.delete');
    Route::get('/staff', fn() => view('staff'))->name('staff');
    Route::get('/history', fn() => view('history'))->name('history');
    Route::get('/settings', fn() => view('settings'))->name('settings');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';