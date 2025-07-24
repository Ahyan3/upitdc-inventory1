<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Issuance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Total Equipment Count with filters
        $equipmentQuery = Equipment::query();

        // Apply equipment filter if requested
        if ($request->has('equipment_filter')) {
            switch ($request->equipment_filter) {
                case 'month':
                    $equipmentQuery->where('created_at', '>=', Carbon::now()->startOfMonth());
                    break;
                case 'year':
                    $equipmentQuery->where('created_at', '>=', Carbon::now()->startOfYear());
                    break;
                case 'quarter':
                    $equipmentQuery->where('created_at', '>=', Carbon::now()->startOfQuarter());
                    break;
            }
        }

        $totalEquipment = $equipmentQuery->count();

        // Active Issuances
        $activeIssuances = Issuance::where('status', 'active')->count();

        // Recent Issuances
        $issuances = Issuance::with(['staff', 'equipment.department'])
            ->latest()
            ->take(10)
            ->get();

        // Inventory Data with filters
        $inventoryQuery = Equipment::with('department');

        // Apply inventory status filter if requested
        if ($request->has('inventory_status') && $request->inventory_status !== 'all') {
            $inventoryQuery->where('status', $request->inventory_status);
        }

        // Apply search if requested
        if ($request->has('inventory_search')) {
            $search = $request->inventory_search;
            $inventoryQuery->where(function ($query) use ($search) {
                $query->where('equipment_name', 'like', "%$search%")
                    ->orWhere('model_brand', 'like', "%$search%")
                    ->orWhere('serial_number', 'like', "%$search%")
                    ->orWhereHas('department', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        $inventory = $inventoryQuery->get();

        // Equipment Issuance Statistics for the Chart
        $equipmentDataQuery = Issuance::join('equipment', 'issuances.equipment_id', '=', 'equipment.id')
            ->select('equipment.equipment_name', DB::raw('count(*) as issuance_count'))
            ->groupBy('equipment.equipment_name');

        // Apply time filter for the chart if requested
        if ($request->has('time_filter')) {
            switch ($request->time_filter) {
                case 'day':
                    $equipmentDataQuery->whereDate('issuances.issued_at', Carbon::today());
                    break;
                case 'week':
                    $equipmentDataQuery->where('issuances.issued_at', '>=', Carbon::now()->startOfWeek());
                    break;
                case 'month':
                    $equipmentDataQuery->where('issuances.issued_at', '>=', Carbon::now()->startOfMonth());
                    break;
                case 'year':
                    $equipmentDataQuery->where('issuances.issued_at', '>=', Carbon::now()->startOfYear());
                    break;
            }
        }

        // Apply type filter for the chart if requested
        if ($request->has('type_filter') && $request->type_filter !== 'total') {
            $equipmentDataQuery->where('equipment.status', $request->type_filter);
        }

        $equipmentData = $equipmentDataQuery->get()->toArray();

        // Log for debugging
        Log::info('Equipment Data for Chart: ', ['data' => $equipmentData]);

        return view('dashboard', [
            'totalEquipment' => $totalEquipment,
            'activeIssuances' => $activeIssuances,
            'issuances' => $issuances,
            'inventory' => $inventory,
            'equipmentData' => $equipmentData, // Add this
            'filters' => $request->only(['equipment_filter', 'inventory_status', 'inventory_search', 'time_filter', 'type_filter']),
            'totalStaff' => \App\Models\Staff::count(),
            'totalIssuedEquipment' => \App\Models\Issuance::where('status', 'issued')->count(),
            'totalReturnedEquipment' => \App\Models\Issuance::where('status', 'returned')->count(),
            'pendingRequests' => \App\Models\Issuance::where('status', 'pending')->count(),
        ]);
    }
}