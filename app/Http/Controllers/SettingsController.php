<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Settings::pluck('value', 'key')->toArray();
        $settingsDetails = Settings::all()->keyBy('key');
        $departments = Department::all();
        return view('settings', compact('settings', 'settingsDetails', 'departments'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'system_title' => 'required|string|max:255',
            'default_return_period' => 'required|integer|min:1',
            'allow_duplicate_pr' => 'required|boolean',
        ]);

        try {
            Settings::updateOrCreate(
                ['key' => 'system_title'],
                [
                    'value' => $validated['system_title'],
                    'type' => 'string',
                    'description' => 'The title displayed in the application header.',
                ]
            );
            Settings::updateOrCreate(
                ['key' => 'default_return_period'],
                [
                    'value' => $validated['default_return_period'],
                    'type' => 'integer',
                    'description' => 'Default number of days for equipment return.',
                ]
            );
            Settings::updateOrCreate(
                ['key' => 'allow_duplicate_pr'],
                [
                    'value' => $validated['allow_duplicate_pr'],
                    'type' => 'boolean',
                    'description' => 'Allow duplicate PR numbers in equipment records.',
                ]
            );
            return redirect()->route('settings')->with('success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,name',
        ]);

        try {
            Department::create(['name' => $validated['department_name']]);
            return redirect()->route('settings')->with('success', 'Department added successfully.');
        } catch (\Exception $e) {
            Log::error('Error adding department: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add department: ' . $e->getMessage());
        }
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);

        try {
            $department->update(['name' => $validated['department_name']]);
            return redirect()->route('settings')->with('success', 'Department updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating department: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update department: ' . $e->getMessage());
        }
    }

    public function destroyDepartment(Department $department)
    {
        try {
            $department->delete();
            return redirect()->route('settings')->with('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting department: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete department: ' . $e->getMessage());
        }
    }
}