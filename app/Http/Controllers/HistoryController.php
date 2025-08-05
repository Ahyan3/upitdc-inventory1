<?php

namespace App\Http\Controllers;

use App\Models\HistoryLog;
use App\Models\Equipment;
use App\Models\User;
use App\Models\Staff;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        // Cache overview stats for 10 minutes
        $overviewStats = Cache::remember('overview_stats', 600, function () {
            return [
                ['label' => 'Total Logs', 'value' => HistoryLog::count()],
                ['label' => 'Inventory Items', 'value' => Equipment::count()],
                ['label' => 'Recent Actions', 'value' => HistoryLog::where('action_date', '>=', now()->subDays(7))->count()],
                ['label' => 'Active Users', 'value' => User::whereHas('historyLogs')->count()],
                ['label' => 'Departments with Items', 'value' => Department::whereHas('equipment')->count()],
            ];
        });

        // History Logs Query
        $historyQuery = HistoryLog::query()->with(['staff', 'equipment']);

        // Apply filters
        if ($request->filled('log_search')) {
            $historyQuery->where('description', 'like', '%' . $request->log_search . '%');
        }

        if ($request->filled('log_user') && $request->log_user !== 'all') {
            $historyQuery->where('staff_id', $request->log_user);
            Log::info('Applied user filter', [
                'staff_id' => $request->log_user,
                'matching_logs_count' => $historyQuery->count(),
            ]);
        }

        if ($request->filled('log_action') && $request->log_action !== 'all') {
            $historyQuery->where('action', $request->log_action);
        }

        if ($request->filled('log_status') && $request->log_status !== 'all') {
            $historyQuery->whereHas('equipment', function ($q) use ($request) {
                $q->where('status', $request->log_status);
            });
        }

        if ($request->filled('log_equipment') && $request->log_equipment !== 'all') {
            $historyQuery->whereHas('equipment', function ($q) use ($request) {
                $q->where('equipment_name', $request->log_equipment);
            });
        }

        if ($request->filled('log_date_from')) {
            $historyQuery->whereDate('action_date', '>=', $request->log_date_from);
        }

        // Apply filters...
        if ($request->filled('log_date_to')) {
            $historyQuery->whereDate('action_date', '<=', $request->log_date_to);
        }

        // Sort Order (default: desc)
        $order = $request->query('order', 'desc');
        $historyQuery->orderBy('action_date', $order);

        // Apply pagination
        $perPage = $request->input('per_page', 20);

        if (!in_array($perPage, [20, 50, 100])) {
            $perPage = 20;
        }
        $history_logs = $historyQuery->paginate($perPage, ['*'], 'history_page')->appends($request->except('history_page'));

        // Inventory Logs Query
        $inventoryQuery = Equipment::query()->with('department');

        if ($request->filled('inventory_search')) {
            $inventoryQuery->where(function ($query) use ($request) {
                $query->where('staff_name', 'like', '%' . $request->inventory_search . '%')
                    ->orWhere('equipment_name', 'like', '%' . $request->inventory_search . '%');
            });
        }

        if ($request->filled('inventory_status') && $request->inventory_status !== 'all') {
            $inventoryQuery->where('status', $request->inventory_status);
        }

        if ($request->filled('inventory_department') && $request->inventory_department !== 'all') {
            $inventoryQuery->where('department_id', $request->inventory_department);
        }

        if ($request->filled('inventory_date_from')) {
            $inventoryQuery->whereDate('date_issued', '>=', $request->inventory_date_from);
        }

        if ($request->filled('inventory_date_to')) {
            $inventoryQuery->whereDate('date_issued', '<=', $request->inventory_date_to);
        }

        $order = $request->query('order', 'desc');
        $inventoryQuery->orderBy('updated_at', $order);

        // Apply pagination for inventory
        $inventoryPerPage = $request->input('inventory_per_page', 20);
        if (!in_array($inventoryPerPage, [20, 50, 100])) {
            $inventoryPerPage = 20;
        }
        $inventory = $inventoryQuery->paginate($inventoryPerPage, ['*'], 'inventory_page')->appends($request->except('inventory_page'));

        // Fetch users and departments for filters
        $users = Staff::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('history', [
            'pageTitle' => 'History Dashboard',
            'headerIcon' => 'fa-history',
            'overviewStats' => $overviewStats,
            'history_logs' => $history_logs,
            'inventory' => $inventory,
            'users' => $users,
            'departments' => $departments,
            'perPage' => $perPage,
            'inventoryPerPage' => $inventoryPerPage,
        ]);
    }


    public function exportHistoryCsv(Request $request)
    {
        try {
            Log::info('Export CSV request received', $request->all());

            $query = HistoryLog::with('user');

            // Apply filters
            if ($request->filled('log_search')) {
                $query->where('description', 'like', '%' . $request->log_search . '%');
            }

            if ($request->filled('log_action') && $request->log_action !== 'all') {
                $query->where('action', $request->log_action);
            }

            if ($request->filled('log_user') && $request->log_user !== 'all') {
                $query->where('user_id', $request->log_user);
            }

            if ($request->filled('log_status') && $request->log_status !== 'all') {
                $query->where('status', $request->log_status);
            }

            if ($request->filled('log_equipment') && $request->log_equipment !== 'all') {
                $query->where('equipment_name', $request->log_equipment);
            }

            if ($request->filled('log_date_from')) {
                $query->whereDate('action_date', '>=', $request->log_date_from);
            }

            if ($request->filled('log_date_to')) {
                $query->whereDate('action_date', '<=', $request->log_date_to);
            }

            $query->latest('action_date');
            $logs = $query->get();

            Log::info('Exporting ' . $logs->count() . ' logs');

            $filename = 'history_logs_export_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function () use ($logs) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Staff Name', 'Action', 'Status', 'Equipment Name', 'Model/Brand', 'Description', 'Action Date']);

                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->user->name ?? 'N/A',
                        ucfirst($log->action),
                        $log->equipment->status ?? 'N/A',
                        $log->equipment->equipment_name ?? 'N/A',
                        ($log->model_brand ?? 'N/A') . ' (ID: ' . ($log->model_id ?? 'N/A') . ')',
                        $log->description ?? 'N/A',
                        optional($log->action_date)->format('Y-m-d H:i:s'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('CSV Export Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Export failed.', 'error' => $e->getMessage()], 500);
        }
    }


    public function exportInventoryCsv(Request $request)
    {
        $query = Equipment::query()->with('department');

        if ($request->filled('inventory_search')) {
            $query->where(function ($query) use ($request) {
                $query->where('staff_name', 'like', '%' . $request->inventory_search . '%')
                    ->orWhere('equipment_name', 'like', '%' . $request->inventory_search . '%');
            });
        }

        if ($request->filled('inventory_status') && $request->inventory_status !== 'all') {
            $query->where('status', $request->inventory_status);
        }

        if ($request->filled('inventory_department') && $request->inventory_department !== 'all') {
            $query->where('department_id', $request->inventory_department);
        }

        if ($request->filled('inventory_date_from')) {
            $query->whereDate('date_issued', '>=', $request->inventory_date_from);
        }

        if ($request->filled('inventory_date_to')) {
            $query->whereDate('date_issued', '<=', $request->inventory_date_to);
        }

        $items = $query->get();

        $filename = 'inventory_logs_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Staff Name', 'Department', 'Equipment', 'Model/Brand', 'Serial No.', 'PR No.', 'Date Issued', 'Status']);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->staff_name ?? 'N/A',
                    $item->department->name ?? 'N/A',
                    $item->equipment_name,
                    $item->model_brand ?? 'N/A',
                    $item->serial_number,
                    $item->pr_number,
                    $item->date_issued instanceof \Carbon\Carbon ? $item->date_issued->format('Y-m-d H:i:s') : ($item->date_issued && \Carbon\Carbon::canBeCreatedFromFormat($item->date_issued, 'Y-m-d H:i:s') ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->date_issued)->format('Y-m-d H:i:s') : 'N/A'),
                    ucfirst(str_replace('_', ' ', $item->status)),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
