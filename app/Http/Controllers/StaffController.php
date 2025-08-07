<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Department;
use App\Models\HistoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;

class StaffController extends Controller
{
    public function index(Request $request)
    {

        Log::info('StaffController: Starting index method', [
            'query' => $request->query(),
            'ip' => $request->ip(),
        ]);

        try {
            DB::connection()->getPdo();

            $validated = $request->validate([
                'search' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:50|exists:departments,name',
                'status' => 'nullable|in:Active,Resigned',
                'per_page' => 'nullable|integer|in:20,50,100',
            ]);

            $perPage = in_array($request->query('per_page'), [20, 50, 100]) ? $request->query('per_page') : 20;
            $cacheKey = 'staff_' . md5(json_encode($request->query()));

            $staff = Cache::remember($cacheKey, now()->addMinutes(1), function () use ($validated, $perPage, $request) {
                $query = Staff::query()->whereNull('deleted_at');

                if ($search = $validated['search'] ?? null) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('department', 'like', "%{$search}%");
                    });
                }
                if ($department = $validated['department'] ?? null) {
                    $query->where('department', $department);
                }
                if ($status = $validated['status'] ?? null) {
                    $query->where('status', $status);
                }

                $order = $request->query('order', 'desc'); 
                $query->orderBy('created_at', $order);

                Log::debug('StaffController: Executing staff query', [
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings(),
                ]);

                return $query->paginate($perPage);
            });

            $departments = Department::orderBy('name')->get();

            $active_staff = Cache::remember('active_staff_count', now()->addMinutes(1), fn() => Staff::whereNull('deleted_at')->where('status', 'Active')->count());
            $resigned_staff = Cache::remember('resigned_staff_count', now()->addMinutes(1), fn() => Staff::whereNull('deleted_at')->where('status', 'Resigned')->count());

            Log::info('StaffController: Staff loaded successfully', [
                'total' => $staff->total(),
                'per_page' => $perPage,
                'current_page' => $staff->currentPage(),
                'staff_data' => $staff->items(),
            ]);

            return view('staff', compact('staff', 'departments', 'active_staff', 'resigned_staff'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('StaffController: Validation failed', [
                'errors' => $e->errors(),
                'query' => $request->query(),
            ]);
            return redirect()->route('staff.index')->with('error', 'Invalid input provided. Please check your filters.');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('StaffController: Database error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('staff.index')->with('error', 'Database error. Please check your connection or contact support.');
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to load staff', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('staff.index')->with('error', 'Failed to load staff data. Please try again or contact support.');
        }
    }

    public function getActiveStaff(Request $request)
    {
        try {
            $activeStaff = Cache::remember('active_staff_list', now()->addMinutes(1), fn() => Staff::whereNull('deleted_at')->where('status', 'Active')->get());
            return response()->json(['status' => 'success', 'data' => $activeStaff]);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to load active staff', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Failed to load active staff'], 500);
        }
    }

    public function getInactiveStaff(Request $request)
    {
        try {
            $inactiveStaff = Cache::remember('inactive_staff_list', now()->addMinutes(1), fn() => Staff::whereNull('deleted_at')->where('status', 'Resigned')->get());
            return response()->json(['status' => 'success', 'data' => $inactiveStaff]);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to load inactive staff', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Failed to load inactive staff'], 500);
        }
    }

    // Export staff equipment
    public function exportAllStaffPDF()
    {
        try {

            $data = [
                'staffList' => Staff::with('department')->get(),
                'exportDate' => now(),
                'title' => 'Complete Staff List',
                'totalStaff' => Staff::count()
            ];

            $pdf = Pdf::loadView('pdf.staff-pdf', $data);
            $pdf->setPaper('A4', 'landscape');
            $filename = 'staff-list-' . now()->format('Y-m-d-H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error exporting staff list PDF: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to export staff list PDF.');
        }
    }

    public function exportStaffLogsPDF($staffId)
{
    try {
        $staff = Staff::findOrFail($staffId);

        $logs = HistoryLog::where('staff_id', $staff->id)
            ->orWhere(function ($query) use ($staff) {
                $query->where('model_brand', 'staff')
                    ->where('model_id', $staff->id);
            })
            ->orWhere(function ($query) use ($staff) {
                $query->where('model_brand', 'equipment')
                    ->whereIn('model_id', function ($subQuery) use ($staff) {
                        $subQuery->select('id')
                            ->from('equipment')
                            ->where('staff_id', $staff->id);
                    });
            })
            ->orderBy('action_date', 'desc')
            ->get()
            ->map(function ($log) {
                $changes = '-';
                try {
                    $oldValues = is_string($log->old_values) ? json_decode($log->old_values, true) : ($log->old_values ?: []);
                    $newValues = is_string($log->new_values) ? json_decode($log->new_values, true) : ($log->new_values ?: []);
                    $changes = collect($newValues)
                        ->map(function ($value, $key) use ($oldValues) {
                            return "{$key}: " . ($oldValues[$key] ?? 'none') . " -> {$value}";
                        })
                        ->implode('; ');
                } catch (\Exception $e) {
                    $changes = $log->model_brand === 'equipment'
                        ? 'Equipment action (details unavailable)'
                        : 'Changed (details unavailable)';
                }

                return (object)[
                    'date' => optional($log->action_date)->format('F d, Y g:i A') ?? 'N/A',
                    'action' => $log->action ?? 'N/A',
                    'model' => ($log->model_brand ?? 'N/A'),
                    'changes' => $changes,
                    'description' => $log->description ?? 'N/A',
                ];
            });

        $data = [
            'staff' => $staff,
            'logs' => $logs,
            'exportDate' => now(),
            'title' => "Staff Log Report - {$staff->name}"
        ];

        $pdf = Pdf::loadView('pdf.staff-log-pdf', $data)->setPaper('A4', 'landscape');
        $filename = 'staff-logs-' . $staff->id . '-' . date('Y-m-d-H-i-s') . '.pdf';

        return $pdf->download($filename);
    } catch (\Exception $e) {
        Log::error('Error exporting Staff Logs PDF: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
        return redirect()->back()->with('error', 'Failed to export Staff Logs PDF: ' . $e->getMessage());
    }
}



    public function validateStaff(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'department' => 'required|string|max:255|exists:departments,name',
                'email' => 'required|email|max:255',
                'status' => 'required|in:Active,Resigned',
            ]);
            return response()->json(['status' => 'success', 'message' => 'Staff data is valid']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to validate staff', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Failed to validate staff'], 500);
        }
    }

    public function checkEmail(Request $request)
    {
        try {
            $validated = $request->validate(['email' => 'required|email']);
            $exists = Staff::where('email', $validated['email'])->whereNull('deleted_at')->exists();
            return response()->json(['status' => 'success', 'exists' => $exists]);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to check email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Failed to check email'], 500);
        }
    }

    public function checkName(Request $request)
    {
        try {
            $validated = $request->validate(['name' => 'required|string|max:255']);
            $exists = Staff::where('name', $validated['name'])->whereNull('deleted_at')->exists();
            return response()->json(['status' => 'success', 'exists' => $exists]);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to check name', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Failed to check name'], 500);
        }
    }

    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255|exists:departments,name',
            'email' => 'required|email|unique:staff,email,NULL,id,deleted_at,NULL',
            'status' => 'required|in:Active,Resigned',
        ]);

        $staff = Staff::create($validated);

        Cache::forget('staff_' . md5(json_encode($request->query())));
        Cache::forget('departments_all');
        Cache::forget('active_staff_count');
        Cache::forget('resigned_staff_count');
        Cache::forget('active_staff_list');
        Cache::forget('inactive_staff_list');

        HistoryLog::create([
            'staff_id' => $staff->id,
            'action' => 'Created',
            'model_brand' => 'staff',
            'model_id' => $staff->id,
            'description' => "Added staff: {$staff->name}",
            'ip_address' => $request->ip(),
            'action_date' => Carbon::now()->toDateTimeString(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Staff added successfully.']);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $e->errors(),
        ], 422);
        
    } catch (\Illuminate\Database\QueryException $e) {
        // Log complete error details to Laravel log
        Log::error('StaffController: Database error while adding staff', [
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage(),
            'sql_state' => $e->errorInfo[0] ?? null,
            'mysql_error_code' => $e->errorInfo[1] ?? null,
            'mysql_error_message' => $e->errorInfo[2] ?? null,
            'request_data' => $request->all(),
            'timestamp' => now(),
            'ip_address' => $request->ip(),
        ]);

        // Log to console in development environment
        if (app()->environment('local', 'development')) {
            error_log("=== STAFF CREATION DATABASE ERROR ===");
            error_log("Email: " . $request->email);
            error_log("Name: " . $request->name);
            error_log("Department: " . $request->department);
            error_log("MySQL Error Code: " . ($e->errorInfo[1] ?? 'N/A'));
            error_log("Full SQL Error: " . $e->getMessage());
            error_log("Request Data: " . json_encode($request->all()));
            error_log("Timestamp: " . now());
            error_log("=====================================");
        }

        // Check if it's a duplicate entry error specifically for email
        if ($e->getCode() == 23000 && 
            (strpos($e->getMessage(), 'Duplicate entry') !== false && 
             strpos($e->getMessage(), 'staff_email_unique') !== false)) {
            
            return response()->json([
                'status' => 'error',
                'message' => 'Email is already taken'
            ], 409);
        }

        // For other database constraint violations
        if ($e->getCode() == 23000) {
            return response()->json([
                'status' => 'error',
                'message' => 'A database constraint was violated. Please check your input.'
            ], 422);
        }

        // For other database errors
        return response()->json([
            'status' => 'error',
            'message' => 'Database error occurred. Please try again.'
        ], 500);
        
    } catch (\Exception $e) {
        Log::error('StaffController: Failed to add staff', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all(),
        ]);
        
        return response()->json([
            'status' => 'error', 
            'message' => 'Failed to add staff: ' . $e->getMessage()
        ], 500);
    }
}


    public function destroy(Request $request, $id)
    {
        try {
            $staff = Staff::whereNull('deleted_at')->findOrFail($id);
            $staffName = $staff->name;
            $staffId = $staff->id;

            $staff->delete();

            Cache::forget('staff_' . md5(json_encode($request->query())));
            Cache::forget('departments_all');
            Cache::forget('active_staff_count');
            Cache::forget('resigned_staff_count');
            Cache::forget('active_staff_list');
            Cache::forget('inactive_staff_list');

            HistoryLog::create([
                'staff_id' => $staffId,
                'action' => 'Deleted',
                'model_brand' => 'staff',
                'model_id' => $staffId,
                'description' => "Deleted staff: {$staffName}",
                'ip_address' => $request->ip(),
                'action_date' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Staff deleted successfully.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('StaffController: Staff not found for deletion', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Staff not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to delete staff', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete staff: ' . $e->getMessage()
            ], 500);
        }
    }

    public function historyLogs(Staff $staff)
    {
        try {
            $logs = HistoryLog::where(function ($query) use ($staff) {
                $query->where('staff_id', $staff->id)
                    ->orWhere(function ($subQuery) use ($staff) {
                        $subQuery->where('model_brand', 'staff')
                            ->where('model_id', $staff->id);
                    });
            })
                ->orderBy('action_date', 'desc')
                ->get()
                ->map(function ($log) {
                    return [
                        'action_date' => optional($log->action_date)->format('Y-m-d H:i:s') ?? 'N/A',
                        'action' => $log->action ?? 'N/A',
                        'model_brand' => $log->model_brand ?? 'N/A',
                        'model_id' => $log->model_id ?? 'N/A',
                        'old_values' => $log->old_values,
                        'new_values' => $log->new_values,
                        'description' => $log->description ?? 'N/A',
                        'ip_address' => $log->ip_address ?? 'N/A',
                    ];
                });

            return response()->json([
                'status' => 'success',
                'staff_name' => $staff->name,
                'logs' => $logs,
            ]);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to load history logs', [
                'staff_id' => $staff->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load history logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, Staff $staff)
    {
        try {
            $validated = $request->validate(['status' => 'required|in:Active,Resigned']);
            $oldStatus = $staff->status;
            $staff->update(['status' => $validated['status']]);

            Cache::forget('staff_' . md5(json_encode($request->query())));
            Cache::forget('departments_all');
            Cache::forget('active_staff_count');
            Cache::forget('resigned_staff_count');
            Cache::forget('active_staff_list');
            Cache::forget('inactive_staff_list');

            HistoryLog::create([
                'staff_id' => $staff->id,
                'action' => 'Status Updated',
                'model_brand' => 'staff',
                'model_id' => $staff->id,
                'old_values' => json_encode(['status' => $oldStatus]),
                'new_values' => json_encode(['status' => $validated['status']]),
                'description' => "Updated status for staff: {$staff->name}",
                'ip_address' => $request->ip(),
                'action_date' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully.',
                'new_status' => $validated['status'],
            ]);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to update status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Failed to update status: ' . $e->getMessage()], 500);
        }
    }

    public function exportCsv()
    {
        try {
            $staff = Staff::whereNull('deleted_at')->get();
            $filename = 'staff_' . now()->format('Y-m-d_His') . '.csv';
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Department', 'Email', 'Status']);
            foreach ($staff as $member) {
                fputcsv($handle, [$member->name, $member->department, $member->email, $member->status]);
            }
            fclose($handle);
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            return response()->streamDownload(function () {
                echo ob_get_clean();
            }, $filename, $headers);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to export CSV', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Failed to export staff data.');
        }
    }

    public function exportHistoryLogs(Staff $staff)
    {
        try {
            $logs = HistoryLog::where('staff_id', $staff->id)
                ->orWhere(function ($query) use ($staff) {
                    $query->where('model_brand', 'staff')
                        ->where('model_id', $staff->id);
                })
                ->orWhere(function ($query) use ($staff) {
                    $query->where('model_brand', 'equipment')
                        ->whereIn('model_id', function ($subQuery) use ($staff) {
                            $subQuery->select('id')
                                ->from('equipment')
                                ->where('staff_id', $staff->id);
                        });
                })
                ->orderBy('action_date', 'desc')
                ->get()
                ->map(function ($log) {
                    $changes = '-';
                    try {
                        $oldValues = is_string($log->old_values) ? json_decode($log->old_values, true) : ($log->old_values ?: []);
                        $newValues = is_string($log->new_values) ? json_decode($log->new_values, true) : ($log->new_values ?: []);
                        $changes = collect($newValues)
                            ->map(function ($value, $key) use ($oldValues) {
                                return "{$key}: " . ($oldValues[$key] ?? 'none') . " -> {$value}";
                            })
                            ->implode('; ');
                    } catch (\Exception $e) {
                        $changes = $log->model_brand === 'equipment' ? 'Equipment action (details unavailable)' : 'Changed (details unavailable)';
                    }
                    return [
                        'Action Date' => optional($log->action_date)->format('Y-m-d H:i:s') ?? 'N/A',
                        'Action' => $log->action ?? 'N/A',
                        'Model' => ($log->model_brand ?? 'N/A'),
                        'Changes' => $changes,
                        'Description' => $log->description ?? 'N/A',
                        'IP Address' => $log->ip_address ?? 'N/A',
                    ];
                });

            Log::info('StaffController: Exporting history logs CSV', [
                'staff_id' => $staff->id,
                'staff_name' => $staff->name,
                'log_count' => $logs->count(),
            ]);

            $filename = "history_logs_{$staff->name}_" . Carbon::now()->format('Ymd_His') . ".csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $callback = function () use ($logs) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Action Date', 'Action', 'Model', 'Changes', 'Description', 'IP Address']);
                foreach ($logs as $log) {
                    fputcsv($file, $log);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to export history logs CSV', [
                'staff_id' => $staff->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export history logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
