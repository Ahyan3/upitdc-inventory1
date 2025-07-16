<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\HistoryLog;
use App\Models\Staff;
use App\Models\User;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        try {
            $equipment = Equipment::with('department')->get();
            $issuances = Issuance::with('equipment')->get();
            Log::info('Inventory index loaded', [
                'equipment_count' => $equipment->count(),
                'issuances_count' => $issuances->count(),
                'user_id' => Auth::id() ?? 'none'
            ]);
            return view('inventory', compact('equipment', 'issuances'));
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
            'status' => 'required|string|in:available,issued',
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

            // Create or find user
            $user = User::firstOrCreate(
                ['email' => $staff->email],
                [
                    'name' => $validated['staff_name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            Log::info('User processed', ['user_id' => $user->id, 'email' => $user->email]);

            // Create equipment
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

            // Create issuance
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

            // Create history log
            HistoryLog::create([
                'action' => 'Issued',
                'action_date' => $validated['date_issued'],
                'model' => 'Equipment',
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
                'model' => 'Equipment',
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

    public function delete(Request $request, Equipment $equipment)
    {
        if (!Auth::check()) {
            Log::error('No authenticated user found for delete');
            return redirect()->route('login')->with('error', 'Please log in to delete equipment.');
        }

        try {
            $oldValues = $equipment->toArray();
            HistoryLog::create([
                'action' => 'Deleted',
                'action_date' => now(),
                'model' => 'Equipment',
                'model_id' => $equipment->id,
                'old_values' => json_encode($oldValues),
                'new_values' => null,
                'user_id' => Auth::id() ?? 1,
                'staff_id' => $equipment->staff_id ?? 1,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "Deleted equipment: {$equipment->equipment_name}, Serial: {$equipment->serial_number}",
            ]);
            Log::info('History log created for delete', ['equipment_id' => $equipment->id]);

            $equipment->delete();
            return redirect()->route('inventory')->with('success', 'Equipment deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in delete: ' . $e->getMessage(), [
                'equipment_id' => $equipment->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to delete equipment: ' . $e->getMessage());
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
                'message' => $serialExists ? 'Serial number already exists' : 
                            ($prExists ? 'PR number already exists' : 'No duplicates found')
            ]);

        } catch (\Exception $e) {
            Log::error('Duplicate check failed: '.$e->getMessage());
            return response()->json([
                'error' => 'Duplicate check failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }

}