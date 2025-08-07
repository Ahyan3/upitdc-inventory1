<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #2c3e50;
        }
        
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        
        .equipment-info {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        
        .equipment-info h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        
        .info-value {
            flex: 1;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        table, th, td {
            border: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        td {
            padding: 6px 8px;
            font-size: 10px;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .no-logs {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-available {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-assigned {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-maintenance {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-damaged {
            background-color: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on: {{ $exportDate->format('F d, Y \a\t g:i A') }}</p>
    </div>

    <div class="equipment-info">
        <h3>Equipment Information</h3>
        <div class="info-row">
            <span class="info-label">Equipment Name:</span>
            <span class="info-value">{{ $equipment->equipment_name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Staff Assigned:</span>
            <span class="info-value">{{ $equipment->staff_name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Department:</span>
            <span class="info-value">{{ $equipment->department->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Model/Brand:</span>
            <span class="info-value">{{ $equipment->model_brand ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Serial Number:</span>
            <span class="info-value">{{ $equipment->serial_number ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">PR Number:</span>
            <span class="info-value">{{ $equipment->pr_number ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Current Status:</span>
            <span class="info-value">
                <span class="status-badge status-{{ strtolower($equipment->status) }}">
                    {{ ucfirst($equipment->status) }}
                </span>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Date Issued:</span>
            <span class="info-value">{{ $equipment->date_issued ? \Carbon\Carbon::parse($equipment->date_issued)->format('M d, Y') : 'N/A' }}</span>
        </div>
    </div>

    @if($logs && $logs->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Staff Member</th>
                    <th style="width: 12%;">Action</th>
                    <th style="width: 15%;">Equipment</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 15%;">Model/Brand</th>
                    <th style="width: 18%;">Description</th>
                    <th style="width: 15%;">Action Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->staff->name ?? 'N/A' }}</td>
                        <td>{{ $log->action ?? 'N/A' }}</td>
                        <td>{{ $log->equipment_name ?? 'N/A' }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($log->status ?? 'unknown') }}">
                                {{ ucfirst($log->status ?? 'N/A') }}
                            </span>
                        </td>
                        <td>{{ $log->model_brand ?? 'N/A' }}</td>
                        <td>{{ $log->description ?? 'N/A' }}</td>
                        <td>{{ $log->action_date ? \Carbon\Carbon::parse($log->action_date)->format('M d, Y g:i A') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-logs">
            <p>No history logs found for this equipment.</p>
        </div>
    @endif

    <div class="footer">
        <p>Equipment Log Report | Total Records: {{ $logs ? $logs->count() : 0 }} | Page 1 of 1</p>
        <p>This report was automatically generated by the Inventory Management System</p>
    </div>
</body>
</html>