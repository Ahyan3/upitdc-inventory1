<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Settings::pluck('value', 'key')->toArray();
        $settingsDetails = Settings::all()->keyBy('key'); // Get full objects keyed by 'key'
        $departments = Department::orderBy('name')->get();

        return view('settings', compact('settings', 'settingsDetails', 'departments'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'system_title' => 'required|string|max:255',
            'default_return_period' => 'required|integer|min:1',
            'allow_duplicate_pr' => 'sometimes|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Log the incoming request for debugging
            Log::info('Settings update request:', $request->all());

            // Update system_title
            $systemTitleSetting = Settings::updateOrCreate(
                ['key' => 'system_title'],
                [
                    'value' => $request->system_title,
                    'type' => 'string',
                    'description' => 'The title displayed in the application header.'
                ]
            );
            
            Log::info('System title setting updated:', $systemTitleSetting->toArray());

            // Update default_return_period
            Settings::updateOrCreate(
                ['key' => 'default_return_period'],
                [
                    'value' => $request->default_return_period,
                    'type' => 'integer',
                    'description' => 'Default number of days for equipment return.'
                ]
            );

            // Update allow_duplicate_pr
            Settings::updateOrCreate(
                ['key' => 'allow_duplicate_pr'],
                [
                    'value' => $request->has('allow_duplicate_pr') ? '1' : '0',
                    'type' => 'boolean',
                    'description' => 'Allow duplicate PR numbers in equipment records.'
                ]
            );

            DB::commit();

            return redirect()->route('settings.index')->with('success', 'Settings updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Settings update failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Failed to update settings. Please try again.');
        }
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,name',
        ]);

        try {
            Department::create([
                'name' => $request->department_name,
            ]);

            return redirect()->route('settings.index')->with('success', 'Department added successfully!');
        } catch (\Exception $e) {
            Log::error('Department creation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add department. Please try again.');
        }
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'department_name' => 'required|string|max:255|unique:departments,name,' . $department->id,
        ]);

        try {
            $department->update([
                'name' => $request->department_name,
            ]);

            return redirect()->route('settings.index')->with('success', 'Department updated successfully!');
        } catch (\Exception $e) {
            Log::error('Department update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update department. Please try again.');
        }
    }

    public function destroyDepartment(Department $department)
    {
        try {
            $department->delete();
            return redirect()->route('settings.index')->with('success', 'Department deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Department deletion failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete department. Please try again.');
        }
    }
}