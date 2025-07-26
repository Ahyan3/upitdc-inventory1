<?php

namespace App\Http\Controllers;

use App\Models\Issuance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IssuanceController extends Controller
{
    public function show($id)
    {
        try {
            $issuance = Issuance::findOrFail($id);
            return response()->json($issuance);
        } catch (\Exception $e) {
            Log::error('IssuanceController: Failed to fetch issuance', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch issuance'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'staff_id' => 'required|exists:staff,id',
                'equipment_id' => 'required|exists:equipment,id',
                'issued_at' => 'required|date',
                'status' => 'required|in:active,returned',
            ]);

            Issuance::create($validated);
            Cache::forget('dashboard_stats');
            Cache::forget('dashboard_counts');

            return response()->json(['status' => 'success', 'message' => 'Issuance added successfully.']);
        } catch (\Exception $e) {
            Log::error('IssuanceController: Failed to add issuance', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to add issuance.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'staff_id' => 'required|exists:staff,id',
                'equipment_id' => 'required|exists:equipment,id',
                'issued_at' => 'required|date',
                'status' => 'required|in:active,returned',
            ]);

            $issuance = Issuance::findOrFail($id);
            $issuance->update($validated);
            Cache::forget('dashboard_stats');
            Cache::forget('dashboard_counts');

            return response()->json(['status' => 'success', 'message' => 'Issuance updated successfully.']);
        } catch (\Exception $e) {
            Log::error('IssuanceController: Failed to update issuance', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to update issuance.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $issuance = Issuance::find($id);
            if (!$issuance) {
                Log::warning('IssuanceController: Attempted to delete non-existent issuance', ['id' => $id]);
                return redirect()->route('dashboard')->with('error', 'Issuance not found.');
            }
            $issuance->delete();
            Cache::forget('dashboard_stats');
            Cache::forget('dashboard_counts');
            Log::info('IssuanceController: Issuance deleted', ['id' => $id]);
            return redirect()->route('dashboard')->with('success', 'Issuance deleted successfully.');
        } catch (\Exception $e) {
            Log::error('IssuanceController: Failed to delete issuance', ['id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'Failed to delete issuance.');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate(['status' => 'required|in:active,returned']);
            $issuance = Issuance::findOrFail($id);
            $issuance->update(['status' => $validated['status']]);
            Cache::forget('dashboard_stats');
            Cache::forget('dashboard_counts');

            return response()->json(['status' => 'success', 'message' => 'Status updated successfully.']);
        } catch (\Exception $e) {
            Log::error('IssuanceController: Failed to update status', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Failed to update status.'], 500);
        }
    }
}
