<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Issuance;
use Carbon\Carbon;

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
                // 'all' is default
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
        if ($request->has('inventory_status')) {
            $inventoryQuery->where('status', $request->inventory_status);
        }
        
        // Apply search if requested
        if ($request->has('inventory_search')) {
            $search = $request->inventory_search;
            $inventoryQuery->where(function($query) use ($search) {
                $query->where('equipment_name', 'like', "%$search%")
                      ->orWhere('model_brand', 'like', "%$search%")
                      ->orWhere('serial_no', 'like', "%$search%")
                      ->orWhereHas('department', function($q) use ($search) {
                          $q->where('name', 'like', "%$search%");
                      });
            });
        }
        
        $inventory = $inventoryQuery->get();

        return view('dashboard', [
            'totalEquipment' => $totalEquipment,
            'activeIssuances' => $activeIssuances,
            'issuances' => $issuances,
            'inventory' => $inventory,
            'filters' => $request->only(['equipment_filter', 'inventory_status', 'inventory_search'])
        ]);
    }
}