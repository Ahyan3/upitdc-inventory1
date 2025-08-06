<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\Staff;
use Carbon\Carbon;
use App\Models\Department;
use App\Models\HistoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        Log::debug('DashboardController: Entering index', ['query' => $request->query()]);
        try {
            $validated = $request->validate([
                'stats_search' => 'nullable|string|max:255',
                'equipment_filter' => 'nullable|in:month,year,quarter',
                'inventory_status' => 'nullable|in:all,available,not_working,working,not_returned,returned',
                'inventory_search' => 'nullable|string|max:255',
                'time_filter' => 'nullable|in:all,day,week,month,year',
                'type_filter' => 'nullable|in:total,active,returned,overdue,lost',

            ]);

            // Stats
            $equipmentQuery = Equipment::query();
            if ($filter = $validated['equipment_filter'] ?? null) {
                $equipmentQuery->where('created_at', '>=', Carbon::now()->{'startOf' . ucfirst($filter)}());
            }

            $stats = [
                'totalEquipment' => $equipmentQuery->count(),
                'totalStaff' => Staff::count(),
                'totalIssuedEquipment' => Equipment::where('status', 'available')->count(),
                'totalReturnedEquipment' => Issuance::where('status', 'returned')->whereNull('deleted_at')->count(),
                'pendingRequests' => Issuance::where('status', 'overdue')->count(),
                'activeIssuances' => Issuance::where('status', 'active')->count(),
                'departmentsWithItems' => Department::whereHas('equipment')->count(),
                'in_use' => Issuance::where('status', 'in_use')->count(),
                'available' => Equipment::where('status', 'Available')->count(),
                'maintenance' => Equipment::where('status', 'Maintenance')->count(),
                'damaged' => Equipment::where('status', 'Damaged')->count(),
                'totalDepartments' => Department::count(),
                'activeStaff' => Staff::where('status', 'Active')->count(),
                'resignedStaff' => Staff::where('status', 'Resigned')->count(),
            ];


            // Issuances
            $issuancesQuery = Issuance::with(['equipment', 'equipment.department']);
            if ($search = $validated['stats_search'] ?? null) {
                $issuancesQuery->whereHas('equipment', fn($q) => $q->where('equipment_name', 'like', "%{$search}%"))
                    ->orWhereHas('staff_name', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }
            if ($type = $validated['type_filter'] ?? null) {
                if ($type !== 'total') {
                    $issuancesQuery->where('status', $type);
                }
            }
            $issuances = $issuancesQuery->latest()->paginate(20);
            Cache::forget('dashboard_stats');

            // Inventory
            $inventoryQuery = Equipment::with(['department', 'issuances.staff']);
            if ($status = $validated['inventory_status'] ?? null) {
                if ($status !== 'all') {
                    $inventoryQuery->where('status', $status);
                }
            }
            if ($search = $validated['inventory_search'] ?? null) {
                $inventoryQuery->where(function ($q) use ($search) {
                    $q->where('equipment_name', 'like', "%{$search}%")
                        ->orWhere('model_brand', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhereHas('department', fn($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('issuances.staff', fn($q) => $q->where('name', 'like', "%{$search}%"));
                });
            }
            $inventory = $inventoryQuery->paginate(20);
            Log::debug('Inventory staff names', [
                'items' => collect($inventory->items())->map(function ($item) {
                    return $item->issuances->first()->staff->name ?? ($item->issuances->isEmpty() ? 'N/A' : 'Unknown');
                })->toArray()
            ]);

            return view('dashboard', array_merge($stats, compact('issuances', 'inventory')));
        } catch (\Exception $e) {
            Log::error('DashboardController: Failed to load dashboard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Failed to load dashboard.');
        }
    }

    public function getCounts()
    {
        try {
            $stats = Cache::remember('dashboard_counts', now()->addSeconds(30), function () {
                return [
                    'totalEquipment' => Equipment::count(),
                    'totalIssuedEquipment' => Issuance::where('status', 'in_used')->count(),
                    'totalReturnedEquipment' => Issuance::where('status', 'returned')->count(),
                    'departmentsWithItems' => Department::whereHas('equipment')->count(),

                    'in_use' => Issuance::where('status', 'in_use')->count(),
                    'available' => Equipment::where('status', 'Available')->count(),
                    'maintenance' => Equipment::where('status', 'Maintenance')->count(),
                    'damaged' => Equipment::where('status', 'Damaged')->count(),

                    'totalDepartments' => Department::count(),
                    'totalStaff' => Staff::count(),
                    'activeStaff' => Staff::where('status', 'Active')->count(),
                    'resignedStaff' => Staff::where('status', 'Resigned')->count(),

                    'recentStaffLogs' => HistoryLog::where('category', 'staff')
                        ->latest()
                        ->take(5)
                        ->get(),

                    'lastUpdated' => now()->toIso8601String(),
                ];
            });

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('DashboardController: Failed to fetch counts', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch counts'], 500);
        }
    }

    public function exportCsv(Request $request)
    {
        try {
            $issuances = Issuance::with(['staff', 'equipment.department'])->latest()->get();
            $filename = 'issuances_' . now()->format('Y-m-d_His') . '.csv';
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Staff Name', 'Department', 'Equipment', 'Model/Brand', 'Serial No.', 'PR No.', 'Date Issued', 'Status']);

            foreach ($issuances as $issuance) {
                fputcsv($handle, [
                    $issuance->staff->name ?? 'N/A',
                    $issuance->equipment->department->name ?? 'N/A',
                    $issuance->equipment->equipment_name ?? 'N/A',
                    $issuance->equipment->model_brand ?? 'N/A',
                    $issuance->equipment->serial_number ?? 'N/A',
                    $issuance->equipment->pr_number ?? 'N/A',
                    $issuance->issued_at->format('Y-m-d'),
                    ucfirst($issuance->status),
                ]);
            }
            fclose($handle);

            return response()->streamDownload(function () {
                echo ob_get_clean();
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        } catch (\Exception $e) {
            Log::error('DashboardController: Failed to export CSV', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to export CSV.');
        }
    }
}
