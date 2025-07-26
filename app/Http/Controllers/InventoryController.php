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

class InventoryController extends Controller
{
    public function index()
    {
        try {
            $departments = Department::all();
            $equipment = Equipment::with('department')->paginate(10);
            $issuances = Issuance::with('equipment')->whereNull('date_returned')->paginate(10);
            $historyLogs = HistoryLog::with(['user', 'staff'])->orderBy('action_date', 'desc')->paginate(10);
            $equipmentData = Equipment::select('equipment_name')
                ->groupBy('equipment_name')
                ->pluck('equipment_name')
                ->mapWithKeys(function ($name) {
                    return [$name => Equipment::where('equipment_name', $name)->count()];
                })->toArray();

            Log::info('Inventory index loaded', [
                'equipment_count' => $equipment->total(),
                'issuances_count' => $issuances->total(),
                'history_logs_count' => $historyLogs->total(),
                'user_id' => Auth::id() ?? 'none'
            ]);

            return view('inventory', compact('equipment', 'issuances', 'departments', 'historyLogs', 'equipmentData'));
        } catch (\Exception $e) {
            Log::error('Error in index: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to load inventory: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            Log::info('Inventory create accessed', ['user_id' => Auth::id() ?? 'none']);
            return view('inventory.issue');
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
            'staff_name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'equipment_name' => 'required|string|max:255',
            'model_brand' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:equipment,serial_number',
            'date_issued' => 'required|date',
            'pr_number' => 'required|string|max:255',
            'status' => 'required|string|in:available,not_working,working,not_returned,returned',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $staff = Staff::firstOrCreate(
                ['name' => $validated['staff_name']],
                [
                    'email' => strtolower(str_replace(' ', '.', $validated['staff_name'])) . '@example.com',
                    'position' => 'Staff',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'remember_token' => null,
                ]
            );
            Log::info('Staff processed', ['staff_id' => $staff->id, 'name' => $staff->name]);

            $user = User::firstOrCreate(
                ['email' => $staff->email],
                [
                    'name' => $validated['staff_name'],
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
                'description' => "Issued equipment: {$equipment->equipment_name}, PR: {$validated['pr_number']}, Serial: {$equipment->serial_number}",
            ]);
            Log::info('History log created', ['model' => 'Equipment', 'model_id' => $equipment->id]);

            DB::commit();
            return redirect()->route('inventory')->with('success', 'Equipment issued successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in issue: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to issue equipment: ' . $e->getMessage())->withInput();
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

    public function destroy(Request $request, Equipment $equipment)
    {
        if (!Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Please log in to delete equipment.'], 401)
                : redirect()->route('login')->with('error', 'Please log in to delete equipment.');
        }

        try {
            HistoryLog::create([
                'action' => 'Deleted',
                'action_date' => now(),
                'model_brand' => $equipment->model_brand,
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
                'equipment_id' => $equipment->id,
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
            Log::info('Equipment edit accessed', ['equipment_id' => $equipment->id, 'user_id' => Auth::id() ?? 'none']);
            return view('inventory.edit', compact('equipment', 'departments'));
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
            'staff_name' => 'required|string|max:255',
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
                'staff_id' => $equipment->staff_id ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "Updated equipment: {$equipment->equipment_name}, PR: {$validated['pr_number']}, Serial: {$equipment->serial_number}",
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
            return redirect()->back()->with('error', 'Failed to update equipment: ' . $e->getMessage())->withInput();
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
        try {
            $timeFilter = $request->query('chart_time', 'month');
            $query = Equipment::select('equipment_name')
                ->groupBy('equipment_name')
                ->pluck('equipment_name')
                ->mapWithKeys(function ($name) use ($timeFilter) {
                    $query = Equipment::where('equipment_name', $name);
                    if ($timeFilter === 'week') {
                        $query->where('date_issued', '>=', now()->subWeek());
                    } elseif ($timeFilter === 'year') {
                        $query->where('date_issued', '>=', now()->subYear());
                    } else {
                        $query->where('date_issued', '>=', now()->subMonth());
                    }
                    return [$name => $query->count()];
                })->toArray();

            return response()->json($query);
        } catch (\Exception $e) {
            Log::error('Error fetching chart data: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
            return response()->json([], 500);
        }
    }
}