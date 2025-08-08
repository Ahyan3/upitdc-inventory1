<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Staff;
use App\Models\HistoryLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PDFController extends Controller
{
    // Export equipment logs
    // Export equipment logs
public function exportEquipmentLogs($id)
{
    try {
        // Load equipment with department relationship - same pattern as CSV export
        $equipment = Equipment::with('department')->findOrFail($id);
        
        // Load logs with staff relationship
        $logs = HistoryLog::with(['staff', 'equipment'])
            ->where('model_id', $equipment->id)
            ->orderBy('action_date', 'desc')
            ->get();

        $data = [
            'equipment' => $equipment,
            'logs' => $logs,
            'exportDate' => now(),
            'title' => "Equipment Log Report - {$equipment->equipment_name}"
        ];

        $pdf = Pdf::loadView('pdf.equipments-log-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        // Fix filename format - remove extra dash and use underscore like CSV export
        $filename = $equipment->equipment_name . '_equipmentlogs-' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);
        
    } catch (\Exception $e) {
        Log::error('Error exporting Equipment Logs PDF: ' . $e->getMessage(), [
            'stack_trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()->with('error', 'Failed to export Equipment Logs PDF: ' . $e->getMessage());
    }
}

    // Export all history logs
    public function exportHistoryLogsPDF()
    {
        try {
            $logs = HistoryLog::with('staff')->get();
            
            $data = [
                'logs' => $logs,
                'exportDate' => now(),
                'title' => 'All History Logs Report',
                'totalItems' => $logs->count()
            ];
            
            $pdf = Pdf::loadView('pdf.history-log-pdf', $data);
            $pdf->setPaper('A4', 'landscape');
            $filename = 'historylogs-' . date('YmdHis') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error exporting History Logs PDF: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to export History Logs PDF: ' . $e->getMessage());
        }
    }


}