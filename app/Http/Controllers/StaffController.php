<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\HistoryLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::when(request('search'), fn($query) => $query->where('name', 'like', '%' . request('search') . '%')
            ->orWhere('department', 'like', '%' . request('search') . '%')
            ->orWhere('email', 'like', '%' . request('search') . '%'))
            ->get();

        $departments = Department::orderBy('name')->get();

        return view('staff', compact('staff', 'departments'));
    }

    public function total()
    {
        $count = DB::table('staff')->count();
        return response()->json(['count' => $count]);
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'email' => 'required|email|unique:staff,email',
                'status' => 'required|in:Active,Resigned',
            ]);

            Staff::create($validated);
            return response()->json(['status' => 'success', 'message' => 'Staff added successfully.'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to add staff member: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'id' => 'required|exists:staff,id',
        'name' => 'required|string|max:255',
        'department' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:staff,email,'.$id,
        'status' => 'required|in:Active,Resigned',
    ]);

    $staff = Staff::findOrFail($id);
    $staff->update($validated);

    return response()->json([
        'status' => 'success',
        'message' => 'Staff member updated successfully!'
    ]);
}

    public function create()
    {
        return view('staff.create');
    }

    public function show(Staff $staff)
    {
        return view('staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        return view('staff.edit', compact('staff'));
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('staff')->with('success', 'Staff deleted successfully.');
    }

    public function historyLogs(Staff $staff)
    {
        try {
            // Get all history logs related to this staff member
            $logs = HistoryLog::where('staff_id', $staff->id)
                ->orWhere(function ($query) use ($staff) {
                    $query->where('model', 'staff')
                        ->where('model_id', $staff->id);
                })
                ->orderBy('action_date', 'desc')
                ->get()
                ->map(function ($log) {
                    return [
                        'action_date' => optional($log->action_date)->format('Y-m-d H:i') ?? 'N/A',
                        'action' => $log->action ?? 'N/A',
                        'model' => $log->model ?? 'N/A',
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
                'logs' => $logs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load history logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, Staff $staff)
    {
        $request->validate(['status' => 'required|in:Active,Resigned']);
        $staff->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'new_status' => $request->status
        ]);
    }
}
