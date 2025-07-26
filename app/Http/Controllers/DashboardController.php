<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
                'chart_time' => 'nullable|in:month,week,year',
            ]);

            // Stats
            $statsCacheKey = 'dashboard_stats_' . md5(json_encode($request->only(['equipment_filter', 'time_filter', 'type_filter'])));
            $stats = Cache::remember($statsCacheKey, now()->addMinutes(10), function () use ($validated) {
                $equipmentQuery = Equipment::query();
                if ($filter = $validated['equipment_filter'] ?? null) {
                    $equipmentQuery->where('created_at', '>=', Carbon::now()->{'startOf' . ucfirst($filter)}());
                }

                return [
                    'totalEquipment' => $equipmentQuery->count(),
                    'totalStaff' => Staff::count(),
                    'totalIssuedEquipment' => Issuance::where('status', 'active')->count(),
                    'totalReturnedEquipment' => Issuance::where('status', 'returned')->count(),
                    'pendingRequests' => Issuance::where('status', 'overdue')->count(),
                    'activeIssuances' => Issuance::where('status', 'active')->count(),
                ];
            });

            // Issuances
            $issuancesQuery = Issuance::with(['staff', 'equipment.department']);
            if ($search = $validated['stats_search'] ?? null) {
                $issuancesQuery->whereHas('staff', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('equipment', fn($q) => $q->where('equipment_name', 'like', "%{$search}%"));
            }
            if ($type = $validated['type_filter'] ?? null) {
                if ($type !== 'total') {
                    $issuancesQuery->where('status', $type);
                }
            }
            $issuances = $issuancesQuery->latest()->paginate(10);
            Cache::forget('dashboard_stats'); // Add to ensure fresh data

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
            $inventory = $inventoryQuery->paginate(10);
            Log::debug('Inventory staff names', [
                'items' => collect($inventory->items())->map(function ($item) {
                    return $item->issuances->first()->staff->name ?? ($item->issuances->isEmpty() ? 'N/A' : 'Unknown');
                })->toArray()
            ]);

            // Chart Data
            $chartCacheKey = 'dashboard_chart_' . ($validated['chart_time'] ?? 'month');
            $equipmentData = Cache::remember($chartCacheKey, now()->addMinutes(10), function () use ($validated) {
                $query = Issuance::join('equipment', 'issuances.equipment_id', '=', 'equipment.id')
                    ->select('equipment.equipment_name', DB::raw('count(*) as issuance_count'))
                    ->groupBy('equipment.equipment_name');
                if ($time = $validated['chart_time'] ?? 'month') {
                    $query->where('issuances.issued_at', '>=', Carbon::now()->{'startOf' . ucfirst($time)}());
                }
                if ($type = $validated['type_filter'] ?? null) {
                    if ($type !== 'total') {
                        $query->where('issuances.status', $type);
                    }
                }
                return $query->get()->pluck('issuance_count', 'equipment_name')->toArray();
            });

            return view('dashboard', array_merge($stats, compact('issuances', 'inventory', 'equipmentData')));
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
            $stats = Cache::remember('dashboard_counts', now()->addMinutes(10), function () {
                return [
                    'totalStaff' => Staff::count(),
                    'totalIssuedEquipment' => Issuance::where('status', 'issued')->count(),
                    'totalReturnedEquipment' => Issuance::where('status', 'returned')->count(),
                    'pendingRequests' => Issuance::where('status', 'pending')->count(),
                    'activeIssuances' => Issuance::where('status', 'active')->count(),
                    'lastUpdated' => now()->toDateTimeString(),
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
