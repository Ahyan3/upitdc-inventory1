<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        // Validate search and per-page inputs
        $validated = $request->validate([
            'department_search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:5|max:50',
        ]);

        try {
            // Default to 10 departments per page, override with validated per_page
            $perPage = $validated['per_page'] ?? 10;

            // Cache departments for 10 minutes
            $departments = Cache::remember('departments_' . md5(json_encode($request->query())), now()->addMinutes(10), function () use ($request, $validated, $perPage) {
                $query = Department::query();

                // Apply search filter for departments
                if ($search = $validated['department_search'] ?? null) {
                    $query->search($search);
                }

                return $query->latest()->paginate($perPage);
            });

            // Get total department count for overview stats
            $totalDepartments = Cache::remember('total_departments', now()->addMinutes(10), function () {
                return Department::count();
            });

            // Log to confirm departments and pagination
            Log::info('SettingsController: departments loaded', [
                'total' => $totalDepartments,
                'per_page' => $perPage,
                'current_page' => $departments->currentPage(),
                'total_pages' => $departments->lastPage(),
                'items' => $departments->count(),
            ]);

            // Retrieve settings from the database
            $settings = Settings::getAllSettings();
            $settings = array_merge([
                'system_title' => config('app.name', 'UPITDC - Inventory System'),
                'default_return_period' => 30,
                'allow_duplicate_pr' => 0,
            ], $settings);

            // Retrieve settings descriptions
            $settingsDetails = [
                'system_title' => (object) ['description' => Settings::where('key', 'system_title')->first()->description ?? 'The title displayed in the application header and browser tab'],
                'default_return_period' => (object) ['description' => Settings::where('key', 'default_return_period')->first()->description ?? 'Set between 7-365 days based on your equipment usage patterns'],
                'allow_duplicate_pr' => (object) ['description' => Settings::where('key', 'allow_duplicate_pr')->first()->description ?? 'Enabling this may cause data integrity issues. Use with caution.'],
            ];

            return view('settings', [
                'departments' => $departments,
                'total_departments' => $totalDepartments,
                'settings' => $settings,
                'settingsDetails' => $settingsDetails,
                'pageTitle' => 'Settings',
                'headerIcon' => 'fa-cog',
                'pageDescription' => 'Configure system settings, manage departments, and customize your inventory management experience',
            ]);
        } catch (\Exception $e) {
            Log::error('SettingsController: Failed to load settings', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to load settings data. Please try again.');
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'system_title' => 'required|string|max:255',
            'default_return_period' => 'required|integer|min:1|max:365',
            'allow_duplicate_pr' => 'boolean',
        ]);

        try {
            // Update settings in the database
            Settings::set('system_title', $validated['system_title'], 'string', 'The title displayed in the application header and browser tab');
            Settings::set('default_return_period', $validated['default_return_period'], 'integer', 'Set between 7-365 days based on your equipment usage patterns');
            Settings::set('allow_duplicate_pr', $validated['allow_duplicate_pr'], 'boolean', 'Enabling this may cause data integrity issues. Use with caution.');

            // Clear cache to reflect updated settings
            Cache::forget('total_departments');
            Cache::forget('departments_' . md5(json_encode($request->query())));

            return redirect()->route('settings')->with('success', 'Settings updated successfully');
        } catch (\Exception $e) {
            Log::error('SettingsController: Failed to update settings', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update settings. Please try again.');
        }
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'department_name' => 'required|string|max:50|unique:departments,name',
        ]);

        try {
            Department::create([
                'name' => $validated['department_name'],
            ]);

            // Clear cache to reflect new department
            Cache::forget('total_departments');
            Cache::forget('departments_' . md5(json_encode($request->query())));

            return redirect()->route('settings')->with('success', 'Department created successfully');
        } catch (\Exception $e) {
            Log::error('SettingsController: Failed to create department', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create department. Please try again.');
        }
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'department_name' => 'required|string|max:50|unique:departments,name,' . $department->id,
        ]);

        try {
            $department->update([
                'name' => $validated['department_name'],
            ]);

            // Clear cache to reflect updated department
            Cache::forget('total_departments');
            Cache::forget('departments_' . md5(json_encode($request->query())));

            return redirect()->route('settings')->with('success', 'Department updated successfully');
        } catch (\Exception $e) {
            Log::error('SettingsController: Failed to update department', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update department. Please try again.');
        }
    }

    public function destroyDepartment(Department $department)
    {
        try {
            $department->delete();

            // Clear cache to reflect deleted department
            Cache::forget('total_departments');

            return redirect()->route('settings')->with('success', 'Department deleted successfully');
        } catch (\Exception $e) {
            Log::error('SettingsController: Failed to delete department', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to delete department. Please try again.');
        }
    }
}