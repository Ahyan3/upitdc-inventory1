<?php

namespace App\Http\Controllers;

use App\Models\HistoryLog;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'log_search' => 'nullable|string|max:255',
            'log_action' => 'nullable|string|in:all,created,updated,deleted',
            'inventory_search' => 'nullable|string|max:255',
            'inventory_status' => 'nullable|string|in:all,available,in_use,maintenance,damaged',
        ]);

        try {
            // Cache history logs for 10 minutes
            $historyLogs = Cache::remember('history_logs_' . md5(json_encode($request->query())), now()->addMinutes(10), function () use ($request, $validated) {
                return HistoryLog::search($validated['log_search'] ?? null)
                                 ->action($validated['log_action'] ?? 'all')
                                 ->latest('action_date')
                                 ->paginate(10);
            });

            // Cache inventory items for 10 minutes
            $inventory = Cache::remember('inventory_' . md5(json_encode($request->query())), now()->addMinutes(10), function () use ($request, $validated) {
                return Equipment::with('department')
                                ->search($validated['inventory_search'] ?? null)
                                ->status($validated['inventory_status'] ?? 'all')
                                ->paginate(10);
            });

            return view('history', [
                'history_logs' => $historyLogs,
                'inventory' => $inventory,
                'pageTitle' => 'History',
                'headerIcon' => 'fa-history',
                'pageDescription' => 'View all inventory actions and current inventory',
                'overviewStats' => [
                    ['label' => 'Total Logs', 'value' => $historyLogs->total()],
                    ['label' => 'Inventory Items', 'value' => $inventory->total()],
                    ['label' => 'System Status', 'value' => '<span class="status-indicator status-active"></span>Active'],
                ],
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load history data. Please try again.');
        }
    }
}