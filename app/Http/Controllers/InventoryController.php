<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\HistoryLog;
use App\Models\Staff;
use App\Models\User;
use App\Models\Settings;
use App\Models\Department;
use App\Http\Controllers\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $users = User::select('id', 'name')->get();
            $departments = Department::all();

            $activeStaff = Cache::remember('active_staff_for_inventory', now()->addMinutes(1), function () {
                return Staff::withoutTrashed()
                    ->where('status', 'Active')
                    ->orderBy('name')
                    ->get(['id', 'name', 'department', 'email']);
            });

            $issuances = Issuance::with(['equipment.department', 'staff'])
                ->whereNull('date_returned')
                ->whereHas('equipment', function ($query) {
                    $query->where('status', 'in_use');
                })
                ->paginate(20, ['*'], 'issuances_page');

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

            if ($request->filled('equipment_name') && $request->equipment_name !== 'all') {
                $query->where('equipment_name', $request->equipment_name);
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

            if ($request->filled('inventory_date_to')) {
                $query->whereDate('action_date', '<=', $request->inventory_date_to);
            }

            // Sort Order (default: desc)
            $order = $request->query('order', 'desc');
            $query->orderBy('date_issued', $order);

            // Apply pagination for inventory 
            $inventoryPerPage = $request->input('inventory_per_page', 20);
            if (!in_array($inventoryPerPage, [20, 50, 100])) {
                $inventoryPerPage = 20;
            }
            $inventory = $query->with(['department', 'issuances.staff'])->paginate($inventoryPerPage, ['*'], 'inventory_page')->appends($request->except('inventory_page'));

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
            $activeStaff = Cache::remember('active_staff_for_inventory', now()->addMinutes(1), function () {
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
            'staff_name' => 'required|exists:staff,name',
            'department_id' => 'required|exists:departments,id',
            'equipment_name' => 'required|string|max:255',
            'model_brand' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:equipment,serial_number',
            'date_issued' => 'required|date',
            'pr_number' => 'required|string|max:255',
            'status' => 'required|string|in:available,in_use,maintenance,damaged,condemned',
            'remarks' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

            // Prepare equipment data
            $equipmentData = [
                'staff_name' => $validated['staff_name'],
                'department_id' => $validated['department_id'],
                'equipment_name' => $validated['equipment_name'],
                'serial_number' => $validated['serial_number'],
                'model_brand' => $validated['model_brand'],
                'status' => $validated['status'],
                'date_issued' => $validated['date_issued'],
                'pr_number' => $validated['pr_number'],
                'remarks' => $validated['remarks'],
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('equipment_images', 'public');
                $equipmentData['image_path'] = $path;
                Log::info('Image uploaded for equipment', ['path' => $path]);
            }

            $equipment = Equipment::create($equipmentData);
            Log::info('Equipment created', ['equipment_id' => $equipment->id]);

            if ($validated['status'] === 'in_use') {
                $issuance = Issuance::create([
                    'user_id' => $user->id,
                    'staff_id' => $staff->id,
                    'equipment_id' => $equipment->id,
                    'issued_at' => $validated['date_issued'],
                    'expected_return_at' => now()->addDays($defaultReturnPeriod),
                    'notes' => ($validated['remarks'] ?? '') . ($validated['pr_number'] ? " (PR: {$validated['pr_number']})" : ''),
                    'status' => 'in_use',
                ]);
                Log::info('Issuance created', ['issuance_id' => $issuance->id]);
            }

            // Update history log description to include image if uploaded
            $logDescription = "Issued equipment: {$equipment->equipment_name} to registered staff {$staff->name} (Department: {$staff->department}), PR: {$validated['pr_number']}, Serial: {$equipment->serial_number}";
            if (isset($equipmentData['image_path'])) {
                $logDescription .= ", Image uploaded: {$equipmentData['image_path']}";
            }

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
                'description' => $logDescription,
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

    public function getReturnableEquipment()
    {
        return Issuance::with(['equipment.department', 'staff'])
            ->whereNull('date_returned')
            ->whereHas('equipment', function ($query) {
                $query->where('status', 'returned');
            })
            ->get();
    }

    public function return(Request $request, Issuance $issuance)
    {
        if (!Auth::check()) {
            Log::error('No authenticated user found for return');
            return redirect()->route('login')->with('error', 'Please log in to return equipment.');
        }

        Log::info('Return request received', [
            'returned_condition' => $request->input('returned_condition'),
            'all_data' => $request->all()
        ]);

        $validated = $request->validate([
            'date_returned' => 'required|date',
            'returned_condition' => 'required|string|in:good,damaged,lost,condemned',
            'remarks' => 'nullable|string|max:500',
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
                'returned_condition' => $validated['returned_condition'],
                'status' => 'returned',
            ]);
            Log::info('Issuance updated', ['issuance_id' => $issuance->id]);

            $equipment->update([
                'status' => 'available',
                'returned_condition' => $validated['returned_condition'],
            ]);

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

    public function show($id)
    {
        try {
            $equipment = Equipment::with('department')->findOrFail($id);
            return response()->json([
                'data' => [
                    'id' => $equipment->id,
                    'equipment_name' => $equipment->equipment_name,
                    'model_brand' => $equipment->model_brand,
                    'serial_number' => $equipment->serial_number,
                    'pr_number' => $equipment->pr_number,
                    'date_issued' => $equipment->date_issued ? $equipment->date_issued->format('Y-m-d H:i:s') : null,
                    'status' => $equipment->status,
                    'staff_name' => $equipment->staff_name,
                    'department_id' => $equipment->department_id,
                    'department_name' => $equipment->department ? $equipment->department->name : null,
                    'remarks' => $equipment->remarks,
                    'image_path' => $equipment->image_path ? Storage::url($equipment->image_path) : null,
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Equipment not found: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['error' => 'Equipment not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching equipment: ' . $e->getMessage(), ['id' => $id]);
            return response()->json(['error' => 'Failed to fetch equipment data'], 500);
        }
    }

    public function edit($id)
    {
        try {
            $equipment = Equipment::with('department')->findOrFail($id);
            return response()->json(['data' => $equipment], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching equipment: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch equipment data'], 404);
        }
    }

    public function apiUpdate(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $equipment = Equipment::findOrFail($id);

        $validated = $request->validate([
            'staff_name' => 'required|string|max:255|exists:staff,name',
            'department_id' => 'required|exists:departments,id',
            'equipment_name' => 'required|string|max:255',
            'model_brand' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:equipment,serial_number,' . $equipment->id,
            'date_issued' => 'nullable|date_format:Y-m-d\TH:i',
            'pr_number' => 'required|string|max:255',
            'status' => 'required|string|in:available,in_use,maintenance,damaged,condemned',
            'remarks' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20048', 
        ]);

        try {
            DB::beginTransaction();

            $staff = Staff::withoutTrashed()
                ->where('name', $validated['staff_name'])
                ->where('status', 'Active')
                ->first();

            if (!$staff) {
                throw new \Exception("Staff member '{$validated['staff_name']}' is not active or missing.");
            }


            if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imagePath = $image->store('equipment_images', 'public');

                    // Delete old image if it exists
                    if ($equipment->image_path && Storage::disk('public')->exists($equipment->image_path)) {
                        Storage::disk('public')->delete($equipment->image_path);
                    }

                    $validated['image_path'] = $imagePath;
                }

            $oldValues = $equipment->toArray();
            $equipment->update($validated);

            if ($validated['status'] === 'available') {
                $issuance = Issuance::where('equipment_id', $equipment->id)
                    ->where('status', 'in_use')
                    ->latest()
                    ->first();

                if ($issuance) {
                    $issuance->update([
                        'status' => 'returned',
                        'returned_at' => now(),
                    ]);
                }

                Log::info('Marked issuance as returned in Edit Equipment', ['equipment_id' => $equipment->id]);
            }

            if ($validated['status'] === 'in_use') {
                // Only create a new issuance if none is active
                $existing = Issuance::where('equipment_id', $equipment->id)
                    ->where('status', 'in_use')
                    ->exists();

                if (!$existing) {
                    Issuance::create([
                        'equipment_id' => $equipment->id,
                        'staff_id' => $staff->id,
                        'user_id' => auth()->id(),
                        'status' => 'in_use',
                        'issued_at' => $validated['date_issued'] ?? now(),
                        'expected_return_at' => now()->addDays(7),
                    ]);

                    Log::info('Created new issuance via Edit Equipment', ['equipment_id' => $equipment->id]);
                }
            }
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

            DB::commit();

            return response()->json(['message' => 'Equipment updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update equipment: ' . $e->getMessage()
            ], 500);
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
            'status' => 'required|string|in:available,in_use,maintenance,damaged,condemned',
            'remarks' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

                 // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('equipment_images', 'public');
                $equipmentData['image_path'] = $path;
                Log::info('Image uploaded for equipment', ['path' => $path]);
            }

                 if ($equipment->image_path && Storage::exists($equipment->image_path)) {
                        Storage::delete($equipment->image_path);
                    }

            $equipment->fill($validated);
            
            if ($request->filled('date_issued')) {
                $equipment->date_issued = Carbon::parse($validated['date_issued'])->setTimezone('Asia/Manila');
            }

            $equipment->save();

            if ($validated['status'] === 'available') {
                // Find the latest active issuance for this equipment
                $issuance = Issuance::where('equipment_id', $equipment->id)
                    ->where('status', 'in_use')
                    ->latest()
                    ->first();

                if ($issuance) {
                    $issuance->update([
                        'status' => 'returned',
                        'returned_at' => now(),
                    ]);
                    Log::info('Issuance marked as returned', ['issuance_id' => $issuance->id]);
                }
            }

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


    public function details($id)
    {
        try {
            $equipment = Equipment::with('department')->findOrFail($id);

            // Use raw DB query for logs if you don't have a model
            $logs = DB::table('equipment_logs')
                ->where('equipment_id', $id)
                ->orderByDesc('created_at')
                ->get();

            $html = '
        <div class="space-y-6">
            <div class="border border-gray-200 rounded-lg p-5 shadow-sm bg-white">
                <h2 class="text-lg font-semibold text-[#00553d] mb-4 flex items-center gap-2">
                    <i class="fas fa-laptop-code text-[#ffcc34]"></i> Equipment Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div><strong>Equipment Name:</strong> ' . e($equipment->equipment_name) . '</div>
                    <div><strong>Model/Brand:</strong> ' . e($equipment->model_brand) . '</div>
                    <div><strong>Serial Number:</strong> ' . e($equipment->serial_number) . '</div>
                    <div><strong>PR Number:</strong> ' . e($equipment->pr_number) . '</div>
                    <div><strong>Date Issued:</strong> ' . $equipment->date_issued->format('F d, Y') . '</div>
                    <div><strong>Status:</strong> 
                        <span class="inline-block px-2 py-1 rounded text-white text-xs ' . $this->statusColor($equipment->status) . '">' .
                ucfirst(str_replace('_', ' ', $equipment->status)) . '
                        </span>
                    </div>
                    <div><strong>Assigned Staff:</strong> ' . e($equipment->staff_name ?? 'N/A') . '</div>
                    <div><strong>Department:</strong> ' . e($equipment->department->name ?? 'N/A') . '</div>
                    <div class="md:col-span-2"><strong>Remarks:</strong> ' . e($equipment->remarks ?? 'None') . '</div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-5 shadow-sm bg-white">
                <h2 class="text-lg font-semibold text-[#00553d] mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-[#ffcc34]"></i> Equipment Logs
                </h2>';

            if ($logs->isEmpty()) {
                $html .= '<div class="text-gray-500 italic text-sm">No logs available for this equipment.</div>';
            } else {
                $html .= '<ul class="space-y-3 text-sm text-gray-800">';
                foreach ($logs as $log) {
                    $html .= '<li class="border-b pb-2">
                            <span class="block font-medium">' . ucfirst($log->action) . '</span>
                            <span class="text-xs text-gray-500">' . date('F d, Y h:i A', strtotime($log->created_at)) . ' by ' . e($log->performed_by ?? 'System') . '</span>';
                    if ($log->notes) {
                        $html .= '<div class="text-gray-600 mt-1 italic">“' . e($log->notes) . '”</div>';
                    }
                    $html .= '</li>';
                }
                $html .= '</ul>';
            }

            $html .= '</div></div>';

            return response($html);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response('<div class="text-red-500">Equipment not found.</div>', 404);
        } catch (\Exception $e) {
            return response('<div class="text-red-500">Something went wrong. Please try again later.</div>', 500);
        }
    }

    public function equipmentLogs(Request $request, $equipment)
    {
        try {
            // First, let's find the equipment
            $equipmentModel = DB::table('equipment')->where('id', $equipment)->first();

            if (!$equipmentModel) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Equipment not found'
                ], 404);
            }

            // Check if there are ANY logs in the table
            $totalLogs = DB::table('history_logs')->count();

            if ($totalLogs === 0) {
                // No logs exist at all - create a sample log for this equipment
                DB::table('history_logs')->insert([
                    'action' => 'created',
                    'action_date' => $equipmentModel->created_at ?? now(),
                    'model_brand' => 'equipment',
                    'model_id' => $equipment,
                    'old_values' => null,
                    'new_values' => json_encode([
                        'equipment_name' => $equipmentModel->equipment_name,
                        'serial_number' => $equipmentModel->serial_number,
                        'status' => $equipmentModel->status
                    ]),
                    'description' => 'Equipment created',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // If equipment has been updated, create update log
                if ($equipmentModel->updated_at !== $equipmentModel->created_at) {
                    DB::table('history_logs')->insert([
                        'action' => 'updated',
                        'action_date' => $equipmentModel->updated_at,
                        'model_brand' => 'equipment',
                        'model_id' => $equipment,
                        'old_values' => json_encode(['status' => 'available']),
                        'new_values' => json_encode(['status' => $equipmentModel->status]),
                        'description' => 'Equipment status updated',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Get all logs for this equipment (try different model_brand values)
            $logs = DB::table('history_logs')
                ->where('model_id', $equipment)
                ->whereIn('model_brand', ['equipment', 'Equipment', 'laptop', 'Laptop', 'printer', 'Printer'])
                ->orderBy('action_date', 'desc')
                ->get();

            // If still no logs, get logs regardless of model_brand
            if ($logs->isEmpty()) {
                $logs = DB::table('history_logs')
                    ->where('model_id', $equipment)
                    ->orderBy('action_date', 'desc')
                    ->get();
            }

            return response()->json([
                'status' => 'success',
                'equipment_name' => $equipmentModel->equipment_name ?? $equipmentModel->serial_number ?? 'Unknown Equipment',
                'logs' => $logs->map(function ($log) {
                    // Ensure old_values and new_values are properly parsed
                    $oldValues = null;
                    $newValues = null;

                    if ($log->old_values) {
                        $oldValues = is_string($log->old_values) ? json_decode($log->old_values, true) : $log->old_values;
                    }

                    if ($log->new_values) {
                        $newValues = is_string($log->new_values) ? json_decode($log->new_values, true) : $log->new_values;
                    }

                    return [
                        'id' => $log->id,
                        'action_date' => $log->action_date,
                        'action' => $log->action,
                        'model_brand' => $log->model_brand,
                        'model_id' => $log->model_id,
                        'old_values' => $oldValues,
                        'new_values' => $newValues,
                        'description' => $log->description ?? 'No description',
                        'created_at' => $log->created_at,
                        'updated_at' => $log->updated_at,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve equipment logs: ' . $e->getMessage()
            ], 500);
        }
    }



    //Inventory CSV Export
    public function exportCsv()
    {
        try {
            $equipment = Equipment::with('department')->whereNull('deleted_at')->get();
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="inventory_export.csv"',
            ];

            $callback = function () use ($equipment) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Staff ID','Equipment Name', 'Staff Name', 'Department', 'Model/Brand', 'Date Issued', 'Serial Number', 'PR Number', 'Status', 'Returned Condition', 'Remarks']);

                foreach ($equipment as $item) {
                    fputcsv($file, [
                        $item->id,
                        $item->equipment_name,
                        $item->staff_name ?? 'N/A',
                        $item->department->name ?? 'N/A',
                        $item->model_brand,
                        $item->date_issued instanceof \Carbon\Carbon ? $item->date_issued->format('Y-m-d H:i:s') : ($item->date_issued ?? 'N/A'),
                        $item->serial_number,
                        $item->pr_number,
                        ucfirst(str_replace('_', ' ', $item->status)),
                        $item->returned_condition ?? 'N/A',
                        $item->remarks ?? 'N/A',
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



    public function exportEquipmentLogs(Request $request, $equipment)
    {
        try {
            // Get the equipment and logs (reuse the logic from equipmentLogs method)
            $equipmentModel = DB::table('equipment')->where('id', $equipment)->first();

            if (!$equipmentModel) {
                return response()->json(['error' => 'Equipment not found'], 404);
            }

            $logs = DB::table('history_logs')
                ->where('model_id', $equipment)
                ->whereIn('model_brand', ['equipment', 'Equipment', 'laptop', 'Laptop', 'printer', 'Printer'])
                ->orderBy('action_date', 'desc')
                ->get();

            if ($logs->isEmpty()) {
                $logs = DB::table('history_logs')
                    ->where('model_id', $equipment)
                    ->orderBy('action_date', 'desc')
                    ->get();
            }

            // Generate CSV content
            $csvData = [];

            // CSV Headers
            $csvData[] = [
                'Date',
                'Action',
                'Model Brand',
                'Model ID',
                'Changes',
                'Description',
                'Created At',
                'Updated At'
            ];

            // Process each log
            foreach ($logs as $log) {
                $changes = '-';

                try {
                    $oldValues = [];
                    $newValues = [];

                    // Properly decode JSON strings to arrays
                    if ($log->old_values) {
                        $decoded = json_decode($log->old_values, true);
                        $oldValues = is_array($decoded) ? $decoded : [];
                    }

                    if ($log->new_values) {
                        $decoded = json_decode($log->new_values, true);
                        $newValues = is_array($decoded) ? $decoded : [];
                    }

                    $changesList = [];
                    $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

                    foreach ($allKeys as $key) {
                        $oldVal = $oldValues[$key] ?? 'none';
                        $newVal = $newValues[$key] ?? 'none';

                        if (
                            in_array($key, [
                                'equipment_name',
                                'model_brand',
                                'serial_number',
                                'pr_number',
                                'status',
                                'returned_condition',
                                'location',
                                'date_issued'
                            ]) &&
                            $oldVal !== $newVal
                        ) {
                            $fieldName = ucwords(str_replace('_', ' ', $key));
                            $changesList[] = "{$fieldName}: {$oldVal} → {$newVal}";
                        }
                    }

                    if (!empty($changesList)) {
                        $changes = implode('; ', $changesList);
                    } else {
                        $changes = match ($log->action) {
                            'created' => 'Equipment created',
                            'updated' => 'Equipment updated',
                            'deleted' => 'Equipment deleted',
                            'issued' => 'Equipment issued',
                            'returned' => 'Equipment returned',
                            default => 'Equipment action performed'
                        };
                    }
                } catch (\Exception $e) {
                    $changes = 'Equipment action (details unavailable)';
                }

                $csvData[] = [
                    $log->action_date ?? '-',
                    $log->action ?? '-',
                    $log->model_brand ?? '-',
                    $log->model_id ?? '-',
                    $changes,
                    $log->description ?? '-',
                    $log->created_at ?? '-',
                    $log->updated_at ?? '-'
                ];
            }

            // Create CSV content
            $output = fopen('php://temp', 'w');
            foreach ($csvData as $row) {
                fputcsv($output, $row);
            }
            rewind($output);
            $csvContent = stream_get_contents($output);
            fclose($output);

            // Generate filename
            $equipmentName = $equipmentModel->equipment_name ?? $equipmentModel->serial_number ?? 'Equipment';
            $filename = "{$equipmentName}_historylogs_" . date('YmdHis') . '.csv';

            // Return CSV download response
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to export logs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportEquipmentsPDF(Request $request)
    {
        try {
            // Get equipment data - same query as your CSV export
            $equipment = Equipment::with('department')->whereNull('deleted_at')->get();
            
            $data = [
                'equipment' => $equipment,
                'exportDate' => now(),
                'totalItems' => $equipment->count(),
                'title' => 'Equipment Inventory Report'
            ];
            
            // Load the view and pass data
            $pdf = Pdf::loadView('pdf.equipments-pdf', $data);
            
            // Set paper size and orientation
            $pdf->setPaper('A4', 'landscape');
            
            // Generate filename with timestamp
            $filename = 'inventorylogs-' . date('YmdHis') . '.pdf';
            
            // Download the PDF
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to export PDF: ' . $e->getMessage());
        }
    }

public function exportEquipmentLogsPDF($equipmentId)
{
    try {
        // Get the equipment
        $equipment = Equipment::with('department')->findOrFail($equipmentId);

        // Match the column used in other log fetching (most likely equipment_id)
        $logs = HistoryLog::with(['staff', 'equipment'])
            ->where('equipment_id', $equipment->id) // Use equipment_id, not model_id
            ->orderBy('action_date', 'desc')
            ->get();

        $data = [
            'equipment' => $equipment,
            'logs' => $logs,
            'exportDate' => now(),
            'title' => "Equipment Log Report - {$equipment->equipment_name}"
        ];

        $pdf = Pdf::loadView('pdf.equipments-log-pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        $filename = $equipment->equipment_name . '_equipmentlogs_' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);

    } catch (\Exception $e) {
        Log::error('Error exporting Equipment Logs PDF: ' . $e->getMessage(), [
            'stack_trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'Failed to export Equipment Logs PDF.');
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
