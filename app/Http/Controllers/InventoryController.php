<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\HistoryLog;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Added import for Log facade

class InventoryController extends Controller
{
    public function index()
    {
        $equipment = Equipment::all();
        $issuances = Issuance::with('equipment')->get();
        return view('inventory', compact('equipment', 'issuances'));
    }

    public function create()
    {
        return view('inventory.issue'); 
    }

    public function issue(Request $request)
    {
        $validated = $request->validate([
            'staff_name' => 'required|string|max:255',
            'department' => 'required|string|in:ITSG,Admin,Content Development,Software Development,Helpdesk,Other',
            'equipment_name' => 'required|string|max:255',
            'model_brand' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:equipment,serial_number',
            'date_issued' => 'required|date',
            'pr_number' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            
            // Create or find staff with all required fields
            $staff = Staff::firstOrCreate(
                ['name' => $validated['staff_name'], 'department' => $validated['department']],
                [
                    'email' => strtolower(str_replace(' ', '.', $validated['staff_name'])) . '@example.com',
                    'position' => 'Staff',
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // Default hashed 'password'
                    'email_verified_at' => now(),
                    'remember_token' => null,
                ]
            );

            // Create or find user with the same information as staff
            $user = User::firstOrCreate(
                ['email' => strtolower(str_replace(' ', '.', $validated['staff_name'])) . '@example.com'],
                [
                    'name' => $validated['staff_name'],
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // Default hashed 'password'
                ]
            );

            // Create equipment
            $equipment = Equipment::create([
                'staff_name' => $request->staff_name,
                'equipment_name' => $request->equipment_name,
                'model_brand' => $request->model_brand,
                'serial_number' => $request->serial_number,
                'department' => $request->department,
                'date_issued' => $request->date_issued,
                'pr_number' => $request->pr_number,
                'remarks' => $request->remarks,
            ]);

            // Create issuance
            $issuance = Issuance::create([
                'user_id' => $user->id,  
                'staff_id' => $staff->id,
                'equipment_id' => $equipment->id,
                'issued_at' => $validated['date_issued'],
                'expected_return_at' => now()->addDays(30),
                'notes' => ($validated['remarks'] ?? '') . ($validated['pr_number'] ? " (PR: {$validated['pr_number']})" : ''),
                'status' => 'active', 
            ]);

            // Log action
            HistoryLog::create([
                'action' => 'Issued',
                'staff_name' => $staff->name,
                'department' => $staff->department,
                'model_brand' => $request->model_brand,
                'user_id' => $user->id, 
                'staff_id' => $staff->id,
                'description' => "PR: {$validated['pr_number']}, Serial: {$equipment->serial_number}",
                'action_date' => $validated['date_issued'],
            ]);

            DB::commit();
            
            return redirect()->route('inventory')->with('success', 'Equipment issued successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in issue: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to issue equipment: ' . $e->getMessage());
        }
    }

    public function return(Request $request, Issuance $issuance)
    {
        $validated = $request->validate([
            'date_returned' => 'required|date',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            $equipment = $issuance->equipment;
            $issuance->update([
                'date_returned' => $validated['date_returned'],
                'remarks' => $validated['remarks'] ?? $issuance->remarks,
            ]);

            HistoryLog::create([
                'action' => 'Returned',
                'staff_name' => $issuance->staff_name,
                'department' => $issuance->department,
                'equipment_name' => $equipment->equipment_name,
                'description' => "PR: {$issuance->pr_number}, Serial: {$equipment->serial_number}",
                'action_date' => $validated['date_returned'],
            ]);

            return redirect()->route('inventory')->with('success', 'Equipment returned successfully.');
        } catch (\Exception $e) {
            Log::error('Error in return: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to return equipment: ' . $e->getMessage());
        }
    }

    public function delete(Equipment $equipment)
    {
        try {
            HistoryLog::create([
                'action' => 'Deleted',
                'staff_name' => 'System',
                'department' => 'N/A',
                'model_brand' => $equipment->model_brand,
                'description' => "Equipment: {$equipment->equipment_name}, Serial: {$equipment->serial_number}",
                'action_date' => now(),
            ]);

            $equipment->delete();
            return redirect()->route('inventory')->with('success', 'Equipment deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in delete: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete equipment: ' . $e->getMessage());
        }
    }

    public function checkDuplicates(Request $request)
    {
        try {
            $serialNumber = $request->input('serial_number');
            $prNumber = $request->input('pr_number');

            $serialExists = Equipment::where('serial_number', $serialNumber)->exists();
            $prExists = Equipment::where('pr_number', $prNumber)->exists();

            return response()->json([
                'serial_exists' => $serialExists,
                'pr_exists' => $prExists
            ]);
        } catch (\Exception $e) {
            Log::error('Error in checkDuplicates: ' . $e->getMessage());
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}