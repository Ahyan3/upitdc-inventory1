<?php

namespace App\Helpers;

use App\Models\HistoryLog;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public static function addToLog($description, $modelBrand, $modelId, $oldValues = null, $newValues = null)
    {
        return HistoryLog::create([
            'user_id' => Auth::id() ?? null,
            'action' => $description,
            'model_brand' => $modelBrand,
            'model_id' => $modelId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'description' => $description,
            'created_at' => now(),
        ]);
    }

    public static function getLogs($modelBrand, $modelId)
    {
        return HistoryLog::where('model_brand', $modelBrand)->where('model_id', $modelId);
    }
}