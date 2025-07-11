<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Issuance;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $issuances = Issuance::with('equipment')->paginate(5);
        $activeIssuances = Issuance::with('equipment')->whereNull('date_returned')->get();
        return view('inventory', compact('issuances', 'activeIssuances'));
    }

    public function issue(Request $request)
    {
        $validated = $request->validate([
            'staff_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'equipment_name' => 'required|string|max:255',
            'equipment_category' => 'required|string|max:255',
            'model_brand' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:equipment,serial_number',
            'date_issued' => 'required|date',
            'pr_number' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        // Create or find equipment
        $equipment = Equipment::firstOrCreate(
            ['serial_number' => $validated['serial_number']],
            [
                'name' => $validated['equipment_name'],
                'category' => $validated['equipment_category'],
                'model_brand' => $validated['model_brand'],
            ]
        );

        // Create issuance
        Issuance::create([
            'staff_name' => $validated['staff_name'],
            'department' => $validated['department'],
            'equipment_id' => $equipment->id,
            'date_issued' => $validated['date_issued'],
            'pr_number' => $validated['pr_number'],
            'remarks' => $validated['remarks'],
        ]);

        return redirect()->route('inventory')->with('success', 'Equipment issued successfully.');
    }

    public function return(Request $request)
    {
        $validated = $request->validate([
            'return_staff_name' => 'required|string|max:255',
            'return_serial_number' => 'required|string|max:255',
            'date_returned' => 'required|date',
            'return_remarks' => 'nullable|string',
        ]);

        $issuance = Issuance::where('staff_name', $validated['return_staff_name'])
            ->whereHas('equipment', function ($query) use ($validated) {
                $query->where('serial_number', $validated['return_serial_number']);
            })
            ->whereNull('date_returned')
            ->firstOrFail();

        $issuance->update([
            'date_returned' => $validated['date_returned'],
            'remarks' => $validated['return_remarks'] ?? $issuance->remarks,
        ]);

        return redirect()->route('inventory')->with('success', 'Equipment returned successfully.');
    }

    public function delete($id)
    {
        $issuance = Issuance::findOrFail($id);
        $issuance->delete();

        return redirect()->route('inventory')->with('success', 'Record deleted successfully.');
    }
}