<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Equipment Inventory Report' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 12px;
            font-size: 9px;
            line-height: 1.2;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #dc3545;
            padding-bottom: 12px;
        }
        
        .header h1 {
            color: #dc3545;
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: bold;
        }
        
        .header-info {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 8px;
            color: #666;
        }
        
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-left: 4px solid #dc3545;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
        }
        
        .summary-line {
            display: flex;
            justify-content: space-around;
            align-items: center;
            gap: 10px;
            font-size: 8px;
            background-color: #fff5f5;
            padding: 8px 10px;
            border: 1px solid #f3cccc;
            border-left: 4px solid #dc3545;
            border-radius: 5px;
        }

        .summary-item {
            display: inline-block;
            font-weight: 600;
            color: #333;
            align-items: center;
            gap: 4px;
            font-size: 8px;
            white-space: nowrap;
        }

        .summary-badge {
            display: inline-block;
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 8px;
            min-width: 20px;
            text-align: center;
        }

        .summary-number {
            font-size: 14px;
            font-weight: bold;
            color: #dc3545;
            display: block;
        }
        
        .summary-label {
            font-size: 7px;
            color: #666;
            margin-top: 2px;
        }
        
        .equipment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 7px;
        }
        
        .equipment-table th {
            background-color: #dc3545;
            color: white;
            padding: 6px 3px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #dc3545;
            font-size: 7px;
        }
        
        .equipment-table td {
            border: 1px solid #ddd;
            padding: 4px 3px;
            vertical-align: top;
            font-size: 7px;
        }
        
        .equipment-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 8px;
            font-size: 6px;
            font-weight: bold;
            text-align: center;
            min-width: 40px;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-available {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-in_use {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #99d1ff;
        }
        
        .status-maintenance {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-damaged {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 12px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 7px;
            color: #666;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 8px;
        }
        
        @page {
            margin: 1cm 0.8cm;
            size: A4 landscape;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* Compact column widths for all fields */
        .col-id { width: 3%; }
        .col-equipment { width: 12%; }
        .col-staff { width: 10%; }
        .col-department { width: 8%; }
        .col-model { width: 10%; }
        .col-date { width: 8%; }
        .col-serial { width: 10%; }
        .col-pr { width: 8%; }
        .col-status { width: 7%; }
        .col-condition { width: 8%; }
        .col-remarks { width: 12%; }
        .col-created { width: 8%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title ?? 'UPITDC Equipment Inventory' }}</h1>
        <div class="header-info">
            <div>Generated: {{ $exportDate->format('F d, Y g:i A') }}</div>
            <div>Total Equipment: {{ $totalItems }}</div>
            <div>Report ID: EQ-{{ $exportDate->format('Ymd-His') }}</div>
        </div>
    </div>

    <div class="summary-box">
        <div class="font-bold" style="margin-bottom: 6px;">Equipment Summary</div>
        <div class="summary-line">
            <div class="summary-item"><span class="summary-badge">{{ $totalItems }}</span> Total Equipment</div>
            <div class="summary-item"><span class="summary-badge">{{ $equipment->where('status', 'available')->count() }}</span> Available</div>
            <div class="summary-item"><span class="summary-badge">{{ $equipment->where('status', 'in_use')->count() }}</span> In Use</div>
            <div class="summary-item"><span class="summary-badge">{{ $equipment->where('status', 'maintenance')->count() }}</span> Maintenance</div>
            <div class="summary-item"><span class="summary-badge">{{ $equipment->where('status', 'damaged')->count() }}</span> Damaged</div>
        </div>
    </div>



    <table class="equipment-table">
        <thead>
            <tr>
                <th class="col-id">ID</th>
                <th class="col-equipment">Equipment Name</th>
                <th class="col-staff">Staff Name</th>
                <th class="col-department">Department</th>
                <th class="col-model">Model/Brand</th>
                <th class="col-date">Date Issued</th>
                <th class="col-serial">Serial Number</th>
                <th class="col-pr">PR Number</th>
                <th class="col-status">Status</th>
                <th class="col-condition">Condition</th>
                <th class="col-remarks">Remarks</th>
                <th class="col-created">Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipment as $item)
            <tr>
                <td class="text-center">{{ $item->id }}</td>
                <td class="font-bold">{{ $item->equipment_name }}</td>
                <td>{{ $item->staff_name ?? 'N/A' }}</td>
                <td>{{ $item->department->name ?? 'N/A' }}</td>
                <td>{{ $item->model_brand }}</td>
                <td class="text-center">
                    {{ $item->date_issued instanceof \Carbon\Carbon ? $item->date_issued->format('M d, Y g:i A') : ($item->date_issued ?? 'N/A') }}
                </td>
                <td class="text-center">{{ $item->serial_number }}</td>
                <td class="text-center">{{ $item->pr_number }}</td>
                <td class="text-center">
                    @php
                        $status = strtolower($item->status ?? 'active');
                        $statusText = ucfirst(str_replace('_', ' ', $status));
                        
                        if (in_array($status, ['active', 'available'])) {
                            $statusClass = 'status-active';
                        } elseif ($status === 'in_use') {
                            $statusClass = 'status-in_use';
                        } elseif ($status === 'maintenance') {
                            $statusClass = 'status-maintenance';
                        } elseif ($status === 'damaged') {
                            $statusClass = 'status-damaged';
                        } else {
                            $statusClass = 'status-damaged';
                        }
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                </td>
                <td class="text-center">{{ $item->returned_condition ?? 'N/A' }}</td>
                <td>{{ Str::limit($item->remarks ?? 'N/A', 25) }}</td>
                <td class="text-center">{{ $item->created_at->format('M d, Y g:i A') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="text-center" style="padding: 15px; font-style: italic; color: #666;">
                    No equipment found in inventory.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-grid">
            <div>
                <strong>Total Equipment:</strong> {{ $totalItems }}<br>
                <strong>Available Equipment:</strong> {{ $equipment->whereIn('status', ['available'])->count() }}
            </div>
            <div>
                <strong>Generated by:</strong> UPITDC Inventory Management System<br>
                <strong>Date:</strong> {{ $exportDate->format('M d, Y') }}
            </div>
            <div>
                <strong>Report Type:</strong> Complete Equipment Inventory<br>
                <strong>Status:</strong> Current as of {{ $exportDate->format('g:i A') }}
            </div>
        </div>
        <hr style="margin: 8px 0; border: none; border-top: 1px solid #ddd;">
        <div>© {{ date('Y') }} University of the Philippines Diliman Information Technology Development Center</div>
        <div>Created by: Ryan Francis C. Romano, Mabini Colleges, Inc.</div>
    </div>
</body>
</html>