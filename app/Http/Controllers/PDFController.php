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
    public function exportEquipmentLogsPDF($equipmentId)
    {
        try {
            $equipment = Equipment::with(['department', 'issuances.staff'])->findOrFail($equipmentId);
            $logs = HistoryLog::with('staff')->where('equipment_id', $equipmentId)->get();
            
            $data = [
                'equipment' => $equipment,
                'logs' => $logs,
                'exportDate' => now(),
                'title' => "Equipment Log Report - {$equipment->equipment_name}"
            ];
            
            $pdf = Pdf::loadView('pdf.equipments-log-pdf', $data);
            $pdf->setPaper('A4', 'landscape');
            $filename = 'equipment-logs-' . $equipment->id . '-' . date('Y-m-d-H-i-s') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error exporting Equipment Logs PDF: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
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
            $filename = 'history-logs-' . date('Y-m-d-H-i-s') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Error exporting History Logs PDF: ' . $e->getMessage(), ['stack_trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to export History Logs PDF: ' . $e->getMessage());
        }
    }


}