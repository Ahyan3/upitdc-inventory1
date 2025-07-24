<?php

namespace App\Http\Controllers;

use App\Models\HistoryLog;
use App\Models\Department;
use App\Models\Equipment;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::query()->with('department');
        if ($search = $request->input('inventory_search')) {
            $query->where('staff_name', 'like', "%{$search}%")
                  ->orWhere('equipment_name', 'like', "%{$search}%")
                  ->orWhere('model_brand', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('pr_number', 'like', "%{$search}%");
        }
        if ($status = $request->input('inventory_status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }
        $inventory = $query->paginate(10); 
        $history_logs = HistoryLog::latest()->paginate(10);
        return view('history', compact('inventory', 'history_logs'));
    }

    public function history()
    {
        $history_logs = HistoryLog::orderBy('action_date', 'desc')->get();
        $inventory_query = Equipment::with('department');

        if (request('inventory_search')) {
            $search = request('inventory_search');
            $inventory_query->where(function ($q) use ($search) {
                $q->where('staff_name', 'like', "%{$search}%")
                    ->orWhere('equipment_name', 'like', "%{$search}%")
                    ->orWhere('model_brand', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('pr_number', 'like', "%{$search}%");
            });
        }

        // Apply status filter if present
        if (request('inventory_status') && request('inventory_status') !== 'all') {
            $inventory_query->where('status', request('inventory_status'));
        }

        $inventory = $inventory_query->get();

        return view('history', compact('history_logs', 'inventory'));
    }
}