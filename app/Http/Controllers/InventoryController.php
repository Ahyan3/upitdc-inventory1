<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\HistoryLog;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'department' => 'required|string|in:IT,Finance,HR,Operations,Marketing,Other',
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

            // Create equipment - FIXED: Added remarks field
            $equipment = Equipment::create([
                'name' => $validated['equipment_name'],
                'model_brand' => $validated['model_brand'], // Use model_brand as shown in the error
                'serial_number' => $validated['serial_number'],
                'remarks' => $validated['remarks'] ?? '', // Add this line to fix the error
            ]);

            // Create issuance
            $issuance = Issuance::create([
                'user_id' => $user->id,  // Use the created/found user ID
                'staff_id' => $staff->id,
                'equipment_id' => $equipment->id,
                'issued_at' => $validated['date_issued'],
                'expected_return_at' => now()->addDays(30), // Set expected return date (30 days from now)
                'notes' => ($validated['remarks'] ?? '') . ($validated['pr_number'] ? " (PR: {$validated['pr_number']})" : ''),
                'status' => 'active', // Try 'active' instead of 'issued'
            ]);

            // Log action
            HistoryLog::create([
                'action' => 'Issued',
                'staff_name' => $staff->name,
                'department' => $staff->department,
                'model' => 'Equipment', // Add model field
                'model_id' => $equipment->id, // Add model_id field
                'user_id' => $user->id, // Add user_id field
                'staff_id' => $staff->id, // Add staff_id field
                'description' => "PR: {$validated['pr_number']}, Serial: {$equipment->serial_number}",
                'action_date' => $validated['date_issued'],
            ]);

            DB::commit();
            
            return redirect()->route('inventory')->with('success', 'Equipment issued successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getTraceAsString()); // This will show you the error
        }
    }

    public function return(Request $request, Issuance $issuance)
    {
        $validated = $request->validate([
            'date_returned' => 'required|date',
            'remarks' => 'nullable|string|max:255',
        ]);

        $equipment = $issuance->equipment;
        $issuance->update([
            'date_returned' => $validated['date_returned'],
            'remarks' => $validated['remarks'] ?? $issuance->remarks,
        ]);

        HistoryLog::create([
            'action' => 'Returned',
            'staff_name' => $issuance->staff_name,
            'department' => $issuance->department,
            'equipment_name' => $equipment->name,
            'details' => "PR: {$issuance->pr_number}, Serial: {$equipment->serial_number}",
            'action_date' => $validated['date_returned'],
        ]);

        return redirect()->route('inventory')->with('success', 'Equipment returned successfully.');
    }

    public function delete(Equipment $equipment)
    {
        HistoryLog::create([
            'action' => 'Deleted',
            'staff_name' => 'System',
            'department' => 'N/A',
            'model' => 'Equipment', // Add model field
            'model_id' => $equipment->id, // Add model_id field
            'description' => "Equipment: {$equipment->name}, Serial: {$equipment->serial_number}",
            'action_date' => now(),
        ]);

        $equipment->delete();
        return redirect()->route('inventory')->with('success', 'Equipment deleted successfully.');
    }
}