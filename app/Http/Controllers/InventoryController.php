<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\HistoryLog;
use App\Models\Staff;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $equipment = Equipment::all();
        $issuances = Issuance::with('equipment')->get();
        return view('inventory', compact('equipment', 'issuances'));
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

        // Create or find staff
        $staff = Staff::firstOrCreate(
            ['name' => $validated['staff_name'], 'department' => $validated['department']],
            ['email' => strtolower(str_replace(' ', '.', $validated['staff_name'])) . '@example.com']
        );

        // Create equipment
        $equipment = Equipment::create([
            'name' => $validated['equipment_name'],
            'model_brand' => $validated['model_brand'],
            'serial_number' => $validated['serial_number'],
        ]);

        // Create issuance
        $issuance = Issuance::create([
            'staff_name' => $staff->name,
            'department' => $staff->department,
            'equipment_id' => $equipment->id,
            'date_issued' => $validated['date_issued'],
            'pr_number' => $validated['pr_number'],
            'remarks' => $validated['remarks'],
        ]);

        // Log action
        HistoryLog::create([
            'action' => 'Issued',
            'staff_name' => $staff->name,
            'department' => $staff->department,
            'equipment_name' => $equipment->name,
            'details' => "PR: {$validated['pr_number']}, Serial: {$equipment->serial_number}",
            'action_date' => $validated['date_issued'],
        ]);

        return redirect()->route('inventory')->with('success', 'Equipment issued successfully.');
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
            'equipment_name' => $equipment->name,
            'details' => "Serial: {$equipment->serial_number}",
            'action_date' => now(),
        ]);

        $equipment->delete();
        return redirect()->route('inventory')->with('success', 'Equipment deleted successfully.');
    }
}