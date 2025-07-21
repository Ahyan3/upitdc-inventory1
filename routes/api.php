<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Models\Staff;
use App\Models\Issuance;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|   
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Dashboard counts with caching and authentication
Route::middleware('auth:sanctum')->get('/dashboard-counts', function () {
    $cacheDuration = 60; // 1 minute cache duration

    return response()->json([
        'totalStaff' => Cache::remember('totalStaff', $cacheDuration, function () {
            return Staff::count();
        }),
        'totalIssuedEquipment' => Cache::remember('totalIssuedEquipment', $cacheDuration, function () {
            return Issuance::where('status', 'issued')->count();
        }),
        'totalReturnedEquipment' => Cache::remember('totalReturnedEquipment', $cacheDuration, function () {
            return Issuance::where('status', 'returned')->count();
        }),
        'pendingRequests' => Cache::remember('pendingRequests', $cacheDuration, function () {
            return Issuance::where('status', 'pending')->count();
        }),
        'lastUpdated' => now()->toDateTimeString()
    ]);
});

Route::get('/total-staff', function () {
    $count = DB::table('staff')->count();
    return response()->json(['count' => $count]);
});
