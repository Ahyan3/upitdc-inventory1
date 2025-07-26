<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Department;
use App\Models\HistoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        Log::info('StaffController: Starting index method', ['query' => $request->query()]);
        try {
            $validated = $request->validate([
                'search' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:50',
                'status' => 'nullable|in:Active,Resigned',
                'per_page' => 'nullable|integer|min:5|max:50',
            ]);

            $perPage = $validated['per_page'] ?? 10;
            $cacheKey = 'staff_' . md5(json_encode($request->query()));

            $staff = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($validated, $perPage) {
                $query = Staff::withoutTrashed(); // Exclude soft-deleted records
                if ($search = $validated['search'] ?? null) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('department', 'like', "%{$search}%");
                    });
                }
                if ($department = $validated['department'] ?? null) {
                    $query->where('department', 'like', "%{$department}%"); // Partial match
                }
                if ($status = $validated['status'] ?? null) {
                    $query->where('status', $status);
                }
                return $query->latest()->paginate($perPage);
            });

            $departments = Cache::remember('departments_all', now()->addMinutes(10), function () {
                return Department::orderBy('name')->get();
            });

            // Calculate overview counts
            $total_staff = Cache::remember('total_staff_count', now()->addMinutes(10), fn() => Staff::withoutTrashed()->count());
            $active_staff = Cache::remember('active_staff_count', now()->addMinutes(10), fn() => Staff::withoutTrashed()->where('status', 'Active')->count());
            $resigned_staff = Cache::remember('resigned_staff_count', now()->addMinutes(10), fn() => Staff::withoutTrashed()->where('status', 'Resigned')->count());

            Log::info('StaffController: staff loaded', [
                'total' => $staff->total(),
                'per_page' => $perPage,
                'current_page' => $staff->currentPage(),
            ]);

            return view('staff', compact('staff', 'departments', 'total_staff', 'active_staff', 'resigned_staff'));
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to load staff', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to load staff data.');
        }
    }

    public function total()
    {
        try {
            $count = Cache::remember('total_staff', now()->addMinutes(10), fn() => Staff::withoutTrashed()->count());
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to load total', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to load staff count'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'department' => 'required|string|max:255|exists:departments,name',
                'email' => 'required|email|unique:staff,email',
                'status' => 'required|in:Active,Resigned',
            ]);

            $staff = Staff::create($validated);
            Cache::forget('total_staff');
            Cache::forget('total_staff_count');
            Cache::forget('active_staff_count');
            Cache::forget('resigned_staff_count');

            HistoryLog::create([
                'staff_id' => $staff->id,
                'action' => 'Created',
                'model_brand' => 'staff',
                'model_id' => $staff->id,
                'description' => "Added staff: {$staff->name}",
                'ip_address' => $request->ip(),
                'action_date' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Staff added successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to add staff', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to add staff: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:staff,id',
                'name' => 'required|string|max:255',
                'department' => 'required|string|max:255|exists:departments,name',
                'email' => 'required|email|max:255|unique:staff,email,' . $id,
                'status' => 'required|in:Active,Resigned',
            ]);

            $staff = Staff::findOrFail($id);
            $oldValues = $staff->getAttributes();
            $staff->update($validated);
            Cache::forget('total_staff');
            Cache::forget('total_staff_count');
            Cache::forget('active_staff_count');
            Cache::forget('resigned_staff_count');

            HistoryLog::create([
                'staff_id' => $staff->id,
                'action' => 'Updated',
                'model_brand' => 'staff',
                'model_id' => $staff->id,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode($validated),
                'description' => "Updated staff: {$staff->name}",
                'ip_address' => $request->ip(),
                'action_date' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Staff updated successfully!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to update staff', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to update staff: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Staff $staff, Request $request)
    {
        try {
            $staffName = $staff->name;
            $staffId = $staff->id;
            $staff->delete();
            Cache::forget('total_staff');
            Cache::forget('total_staff_count');
            Cache::forget('active_staff_count');
            Cache::forget('resigned_staff_count');
            // Invalidate staff list cache
            Cache::tags(['staff'])->flush();

            HistoryLog::create([
                'staff_id' => $staffId,
                'action' => 'Deleted',
                'model_brand' => 'staff',
                'model_id' => $staffId,
                'description' => "Deleted staff: {$staffName}",
                'ip_address' => $request->ip(),
                'action_date' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Staff deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to delete staff', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to delete staff: ' . $e->getMessage()], 500);
        }
    }

    public function historyLogs(Staff $staff)
    {
        try {
            $logs = HistoryLog::where('staff_id', $staff->id)
                ->orWhere(function ($query) use ($staff) {
                    $query->where('model_brand', 'staff')
                          ->where('model_id', $staff->id);
                })
                ->orderBy('action_date', 'desc')
                ->get()
                ->map(function ($log) {
                    return [
                        'action_date' => optional($log->action_date)->format('Y-m-d H:i') ?? 'N/A',
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
            Log::error('StaffController: Failed to load history logs', ['error' => $e->getMessage()]);
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
            Cache::forget('total_staff');
            Cache::forget('total_staff_count');
            Cache::forget('active_staff_count');
            Cache::forget('resigned_staff_count');

            HistoryLog::create([
                'staff_id' => $staff->id,
                'action' => 'Status Updated',
                'model_brand' => 'staff',
                'model_id' => $staff->id,
                'old_values' => json_encode(['status' => $oldStatus]),
                'new_values' => json_encode(['status' => $validated['status']]),
                'description' => "Updated status for staff: {$staff->name}",
                'ip_address' => $request->ip(),
                'action_date' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status updated successfully.',
                'new_status' => $validated['status'],
            ]);
        } catch (\Exception $e) {
            Log::error('StaffController: Failed to update status', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to update status: ' . $e->getMessage()], 500);
        }
    }

    public function exportCsv() {
    try {
        $staff = Staff::withoutTrashed()->get();
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
        Log::error('StaffController: Failed to export CSV', ['error' => $e->getMessage()]);
        return back()->with('error', 'Failed to export staff data.');
    }
}
}