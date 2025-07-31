<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\HistoryLog;
use App\Models\Staff;
use App\Models\User;
use App\Models\Settings;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $users = User::select('id', 'name')->get();

            $departments = Department::all();

            // Fetch ACTIVE staff members for equipment assignment
            $activeStaff = Cache::remember('active_staff_for_inventory', now()->addMinutes(10), function () {
                return Staff::withoutTrashed()
                    ->where('status', 'Active')
                    ->orderBy('name')
                    ->get(['id', 'name', 'department', 'email']);
            });

            $issuances = Issuance::with(['equipment.department', 'staff'])
                ->whereNull('date_returned')
                ->paginate(20, ['*'], 'issuances_page');

            // Build inventory query with filters
            $query = Equipment::query();

            // Apply filters from the request
            if ($request->filled('inventory_search')) {
                $search = $request->input('inventory_search');
                $query->where(function ($q) use ($search) {
                    $q->where('equipment_name', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhere('pr_number', 'like', "%{$search}%")
                        ->orWhereHas('issuances.staff', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->filled('inventory_status') && $request->input('inventory_status') != 'all') {
                $query->where('status', $request->input('inventory_status'));
            }

            if ($request->filled('inventory_department') && $request->input('inventory_department') != 'all') {
                $query->where('department_id', $request->input('inventory_department'));
            }

            if ($request->filled('inventory_user') && $request->input('inventory_user') != 'all') {
                $query->whereHas('issuances', function ($q) use ($request) {
                    $q->where('staff_id', $request->input('inventory_user'));
                });
            }

            if ($request->filled('inventory_date_from')) {
                $query->whereDate('date_issued', '>=', $request->input('inventory_date_from'));
            }

            // Apply pagination for inventory 
            $inventoryPerPage = $request->input('inventory_per_page', 20);
            if (!in_array($inventoryPerPage, [20, 50, 100])) {
                $inventoryPerPage = 20;
            }
            $inventory = $query->with('department')->paginate($inventoryPerPage, ['*'], 'inventory_page')->appends($request->except('inventory_page'));

            // Equipment data for the chart
            $equipmentData = Equipment::groupBy('equipment_name')
                ->selectRaw('equipment_name, COUNT(*) as count')
                ->pluck('count', 'equipment_name')
                ->toArray();

            Log::info('Inventory index loaded', [
                'inventory_count' => $inventory->total(),
                'issuances_count' => $issuances->total(),
                'active_staff_count' => $activeStaff->count(),
                'user_id' => Auth::id() ?? 'none'
            ]);

            return view('inventory', [
                'inventory' => $inventory,
                'departments' => $departments,
                'issuances' => $issuances,
                'equipmentData' => $equipmentData,
                'users' => $users,
                'activeStaff' => $activeStaff, 
                'inventoryPerPage' => $inventoryPerPage,
                'staffValidationMessage' => 'Only registered active staff members can be issued equipment. If the person is not listed below, please register them in the Staff tab first with their consent.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error in index: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to load inventory: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            // Get active staff and departments for the form
            $activeStaff = Cache::remember('active_staff_for_inventory', now()->addMinutes(10), function () {
                return Staff::withoutTrashed()
                    ->where('status', 'Active')
                    ->orderBy('name')
                    ->get(['id', 'name', 'department', 'email']);
            });

            $departments = Department::orderBy('name')->get();

            Log::info('Inventory create accessed', [
                'active_staff_count' => $activeStaff->count(),
                'user_id' => Auth::id() ?? 'none'
            ]);

            return view('inventory.issue', [
                'activeStaff' => $activeStaff,
                'departments' => $departments,
                'staffValidationMessage' => 'Only registered active staff members can be issued equipment. If the person is not listed, please register them in the Staff tab first with their consent.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in create: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to load issue form: ' . $e->getMessage());
        }
    }

    public function issue(Request $request)
    {
        if (!Auth::check()) {
            Log::error('No authenticated user found for issue');
            return redirect()->route('login')->with('error', 'Please log in to issue equipment.');
        }

        $defaultReturnPeriod = Settings::where('key', 'default_return_period')->value('value') ?? 30;

        $validated = $request->validate([
            'staff_name' => 'required|string|max:255|exists:staff,name', 
            'department_id' => 'required|exists:departments,id',
            'equipment_name' => 'required|string|max:255',
            'model_brand' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:equipment,serial_number',
            'date_issued' => 'required|date',
            'pr_number' => 'required|string|max:255',
            'status' => 'required|string|in:available,in_use,maintenance,damaged',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Get the existing staff member (must be registered and active)
            $staff = Staff::withoutTrashed()
                ->where('name', $validated['staff_name'])
                ->where('status', 'Active')
                ->first();

            if (!$staff) {
                throw new \Exception("Staff member '{$validated['staff_name']}' is not found or not active. Please ensure they are registered in the Staff tab and have an Active status.");
            }

            Log::info('Staff validated for equipment issue', [
                'staff_id' => $staff->id,
                'name' => $staff->name,
                'status' => $staff->status,
                'department' => $staff->department
            ]);

            // Verify the staff member's department matches the selected department
            $selectedDepartment = Department::find($validated['department_id']);
            if ($staff->department !== $selectedDepartment->name) {
                Log::warning('Department mismatch detected', [
                    'staff_department' => $staff->department,
                    'selected_department' => $selectedDepartment->name,
                    'staff_name' => $staff->name
                ]);
            }

            // Create or get the corresponding user
            $user = User::firstOrCreate(
                ['email' => $staff->email],
                [
                    'name' => $staff->name,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            Log::info('User processed', ['user_id' => $user->id, 'email' => $user->email]);

            $equipment = Equipment::create([
                'staff_name' => $validated['staff_name'],
                'department_id' => $validated['department_id'],
                'equipment_name' => $validated['equipment_name'],
                'serial_number' => $validated['serial_number'],
                'model_brand' => $validated['model_brand'],
                'status' => $validated['status'],
                'date_issued' => $validated['date_issued'],
                'pr_number' => $validated['pr_number'],
                'remarks' => $validated['remarks'],
            ]);
            Log::info('Equipment created', ['equipment_id' => $equipment->id]);

            $issuance = Issuance::create([
                'user_id' => $user->id,
                'staff_id' => $staff->id,
                'equipment_id' => $equipment->id,
                'issued_at' => $validated['date_issued'],
                'expected_return_at' => now()->addDays($defaultReturnPeriod),
                'notes' => ($validated['remarks'] ?? '') . ($validated['pr_number'] ? " (PR: {$validated['pr_number']})" : ''),
                'status' => 'active',
            ]);
            Log::info('Issuance created', ['issuance_id' => $issuance->id]);

            HistoryLog::create([
                'action' => 'Issued',
                'action_date' => $validated['date_issued'],
                'model_brand' => $validated['model_brand'],
                'model_id' => $equipment->id,
                'old_values' => null,
                'new_values' => json_encode($validated),
                'user_id' => $user->id,
                'staff_id' => $staff->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "Issued equipment: {$equipment->equipment_name} to registered staff {$staff->name} (Department: {$staff->department}), PR: {$validated['pr_number']}, Serial: {$equipment->serial_number}",
            ]);
            Log::info('History log created', ['model' => 'Equipment', 'model_id' => $equipment->id]);

            Cache::forget('active_staff_for_inventory');

            DB::commit();
            return redirect()->route('inventory')->with('success', "Equipment issued successfully to {$staff->name} ({$staff->department}).");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in issue: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to issue equipment: ' . $e->getMessage())
                ->with('info', 'Remember: Only registered active staff members can be issued equipment. Please check the Staff tab if the person is not listed.')
                ->withInput();
        }
    }

    public function getActiveStaff()
    {
        try {
            $staff = Cache::remember('active_staff_for_inventory', now()->addMinutes(10), function () {
                return Staff::withoutTrashed()
                    ->where('status', 'Active')
                    ->orderBy('name')
                    ->get(['id', 'name', 'department', 'email']);
            });

            return response()->json([
                'status' => 'success',
                'data' => $staff,
                'message' => 'Only registered active staff members can be assigned equipment. If the person is not listed, please register them in the Staff tab first with their consent.'
            ]);
        } catch (\Exception $e) {
            Log::error('InventoryController: Failed to get active staff', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load staff data.'
            ], 500);
        }
    }

    public function validateStaffForEquipment(Request $request)
    {
        try {
            $request->validate([
                'staff_name' => 'required|string|max:255',
            ]);

            $staff = Staff::withoutTrashed()
                ->where('name', $request->staff_name)
                ->where('status', 'Active')
                ->first(['id', 'name', 'department', 'email', 'status']);

            if (!$staff) {
                return response()->json([
                    'status' => 'error',
                    'valid' => false,
                    'message' => "Staff member '{$request->staff_name}' is not registered or not active. Please register them in the Staff tab first with their consent."
                ]);
            }

            return response()->json([
                'status' => 'success',
                'valid' => true,
                'staff' => $staff,
                'message' => 'Staff member is valid for equipment assignment.'
            ]);
        } catch (\Exception $e) {
            Log::error('InventoryController: Staff validation failed', [
                'staff_name' => $request->staff_name ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'status' => 'error',
                'valid' => false,
                'message' => 'Failed to validate staff member.'
            ], 500);
        }
    }

    public function return(Request $request, Issuance $issuance)
    {
        if (!Auth::check()) {
            Log::error('No authenticated user found for return');
            return redirect()->route('login')->with('error', 'Please log in to return equipment.');
        }

        $validated = $request->validate([
            'date_returned' => 'required|date',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            if (!$issuance->equipment) {
                throw new \Exception('Associated equipment not found for issuance ID: ' . $issuance->id);
            }

            $equipment = $issuance->equipment;
            $oldValues = $issuance->only(['status', 'notes', 'returned_at', 'date_returned']);
            $issuance->update([
                'date_returned' => $validated['date_returned'],
                'returned_at' => $validated['date_returned'],
                'return_notes' => $validated['remarks'] ?? $issuance->return_notes,
                'status' => 'returned',
            ]);
            Log::info('Issuance updated', ['issuance_id' => $issuance->id]);

            $equipment->update(['status' => 'available']);
            Log::info('Equipment updated', ['equipment_id' => $equipment->id]);

            HistoryLog::create([
                'action' => 'Returned',
                'action_date' => $validated['date_returned'],
                'model_brand' => $equipment->model_brand,
                'model_id' => $equipment->id,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode(['status' => 'returned', 'return_notes' => $validated['remarks']]),
                'user_id' => Auth::id() ?? $issuance->user_id,
                'staff_id' => $issuance->staff_id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "Returned equipment: {$equipment->equipment_name}, PR: {$equipment->pr_number}, Serial: {$equipment->serial_number}",
            ]);
            Log::info('History log created for return', ['issuance_id' => $issuance->id]);

            return redirect()->route('inventory')->with('success', 'Equipment returned successfully.');
        } catch (\Exception $e) {
            Log::error('Error in return: ' . $e->getMessage(), [
                'issuance_id' => $issuance->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to return equipment: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Please log in to delete equipment.'], 401)
                : redirect()->route('login')->with('error', 'Please log in to delete equipment.');
        }

        try {
            Log::info('Attempting to delete equipment', ['id' => $id]);

            $equipment = Equipment::find($id);

            if (!$equipment) {
                Log::warning('Equipment not found for deletion', ['id' => $id]);
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Equipment not found or already deleted.'], 404)
                    : redirect()->back()->with('error', 'Equipment not found or already deleted.');
            }

            Log::info('Equipment found for deletion', ['equipment' => $equipment->toArray()]);

            HistoryLog::create([
                'action' => 'Deleted',
                'action_date' => now(),
                'model_brand' => 'Equipment',
                'model_id' => $equipment->id,
                'old_values' => json_encode($equipment->toArray()),
                'new_values' => null,
                'user_id' => Auth::id(),
                'staff_id' => $equipment->staff_id ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "Deleted equipment: {$equipment->equipment_name}",
            ]);

            $equipment->delete();

            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Equipment deleted successfully'])
                : redirect()->route('inventory')->with('success', 'Equipment deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error in destroy: ' . $e->getMessage(), [
                'equipment_id' => $id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Failed to delete equipment: ' . $e->getMessage()], 500)
                : redirect()->back()->with('error', 'Failed to delete equipment: ' . $e->getMessage());
        }
    }

    public function checkDuplicates(Request $request)
    {
        try {
            $request->validate([
                'serial_number' => 'required|string',
                'pr_number' => 'required|string'
            ]);

            $serialExists = Equipment::where('serial_number', $request->serial_number)->exists();
            $prExists = Equipment::where('pr_number', $request->pr_number)->exists();

            return response()->json([
                'serial_exists' => $serialExists,
                'pr_exists' => $prExists,
                'message' => $serialExists ? 'Serial number already exists' : ($prExists ? 'PR number already exists' : 'No duplicates found')
            ]);
        } catch (\Exception $e) {
            Log::error('Duplicate check failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Duplicate check failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function view(Equipment $equipment)
    {
        try {
            Log::info('Equipment viewed', ['equipment_id' => $equipment->id, 'user_id' => Auth::id() ?? 'none']);
            return view('inventory.view', compact('equipment'));
        } catch (\Exception $e) {
            Log::error('Error in view: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to load equipment: ' . $e->getMessage());
        }
    }

    public function edit(Equipment $equipment)
    {
        try {
            $departments = Department::all();

            // Get active staff for editing form
            $activeStaff = Cache::remember('active_staff_for_inventory', now()->addMinutes(10), function () {
                return Staff::withoutTrashed()
                    ->where('status', 'Active')
                    ->orderBy('name')
                    ->get(['id', 'name', 'department', 'email']);
            });

            Log::info('Equipment edit accessed', ['equipment_id' => $equipment->id, 'user_id' => Auth::id() ?? 'none']);

            return view('inventory.edit', compact('equipment', 'departments', 'activeStaff'));
        } catch (\Exception $e) {
            Log::error('Error in edit: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to load edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Equipment $equipment)
    {
        if (!Auth::check()) {
            Log::error('No authenticated user found for update');
            return redirect()->route('login')->with('error', 'Please log in to update equipment.');
        }

        $validated = $request->validate([
            'staff_name' => 'required|string|max:255|exists:staff,name',
            'department_id' => 'required|exists:departments,id',
            'equipment_name' => 'required|string|max:255',
            'model_brand' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:equipment,serial_number,' . $equipment->id,
            'date_issued' => 'required|date',
            'pr_number' => 'required|string|max:255',
            'status' => 'required|string|in:available,not_working,working,not_returned,returned',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Validate that the staff member exists and is active
            $staff = Staff::withoutTrashed()
                ->where('name', $validated['staff_name'])
                ->where('status', 'Active')
                ->first();

            if (!$staff) {
                throw new \Exception("Staff member '{$validated['staff_name']}' is not found or not active. Please ensure they are registered in the Staff tab.");
            }

            $oldValues = $equipment->toArray();
            $equipment->update($validated);
            Log::info('Equipment updated', ['equipment_id' => $equipment->id]);

            HistoryLog::create([
                'action' => 'Updated',
                'action_date' => now(),
                'model_brand' => $validated['model_brand'],
                'model_id' => $equipment->id,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode($validated),
                'user_id' => Auth::id(),
                'staff_id' => $staff->id, 
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "Updated equipment: {$equipment->equipment_name} for {$staff->name} ({$staff->department}), PR: {$validated['pr_number']}, Serial: {$equipment->serial_number}",
            ]);
            Log::info('History log created for update', ['equipment_id' => $equipment->id]);

            DB::commit();
            return redirect()->route('inventory')->with('success', 'Equipment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in update: ' . $e->getMessage(), [
                'equipment_id' => $equipment->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update equipment: ' . $e->getMessage())
                ->with('info', 'Remember: Only registered active staff members can be assigned equipment.')
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $equipment = Equipment::findOrFail($id);
            return response()->json($equipment);
        } catch (\Exception $e) {
            Log::error('InventoryController: Failed to fetch equipment', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch equipment'], 500);
        }
    }

    public function exportCsv()
    {
        try {
            $equipment = Equipment::with('department')->get();
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="inventory_export.csv"',
            ];

            $callback = function () use ($equipment) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Staff Name', 'Department', 'Equipment Name', 'Model/Brand', 'Date Issued', 'Serial Number', 'PR Number', 'Status']);

                foreach ($equipment as $item) {
                    fputcsv($file, [
                        $item->staff_name ?? 'N/A',
                        $item->department->name ?? 'N/A',
                        $item->equipment_name,
                        $item->model_brand,
                        $item->date_issued instanceof \Carbon\Carbon ? $item->date_issued->format('Y-m-d') : ($item->date_issued ?? 'N/A'),
                        $item->serial_number,
                        $item->pr_number,
                        ucfirst(str_replace('_', ' ', $item->status)),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting CSV: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to export inventory: ' . $e->getMessage());
        }
    }

    public function chartData(Request $request)
    {
        $timeRange = $request->query('time_range', 'month');
        $startDate = now();
        $groupByFormat = 'Y-m-d';

        switch ($timeRange) {
            case 'week':
                $startDate = now()->subWeek();
                $groupByFormat = 'Y-m-d';
                break;
            case 'year':
                $startDate = now()->subYear();
                $groupByFormat = 'Y-m';
                break;
            case 'past_year':
                $startDate = now()->subYear();
                $groupByFormat = 'Y-m';
                break;
            case 'month':
            default:
                $startDate = now()->subMonth();
                $groupByFormat = 'Y-m-d';
                break;
        }

        $issuances = Issuance::where('created_at', '>=', $startDate)
            ->with('equipment')
            ->get()
            ->groupBy(function ($issuance) use ($groupByFormat) {
                return $issuance->created_at->format($groupByFormat);
            })
            ->map(function ($group) {
                return $group->countBy(function ($issuance) {
                    return $issuance->equipment->equipment_name ?? 'Unknown';
                });
            });

        $equipmentData = [];
        foreach ($issuances as $date => $counts) {
            foreach ($counts as $equipment => $count) {
                if (!isset($equipmentData[$equipment])) {
                    $equipmentData[$equipment] = 0;
                }
                $equipmentData[$equipment] += $count;
            }
        }

        return response()->json([
            'equipmentData' => $equipmentData
        ]);
    }
}