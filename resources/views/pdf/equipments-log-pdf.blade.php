<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9px;
            color: #333;
            padding: 16px;
            margin: 0;
        }
        h1 {
            font-size: 18px;
            color: #dc3545;
            text-align: center;
            margin-bottom: 8px;
        }
        .header-info {
            font-size: 8px;
            color: #666;
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .equipment-info {
            font-size: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 4px;
        }
        .equipment-info h3 {
            font-size: 11px;
            font-weight: bold;
            color: #dc3545;
            margin: 0 0 6px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .info-label {
            font-weight: bold;
            width: 100px;
        }
        .info-value {
            flex: 1;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8px;
        }
        .table th {
            background-color: #dc3545;
            color: white;
            padding: 6px 4px;
            text-align: left;
            border: 1px solid #dc3545;
            font-weight: bold;
        }
        .table td {
            padding: 5px 4px;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .no-logs {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
            font-size: 8px;
        }
        .footer {
            margin-top: 20px;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .footer-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .status-badge {
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
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
        hr {
            margin: 8px 0;
            border: none;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'Equipment Log Report' }}</h1>

    <div class="header-info">
        <div>Generated: {{ $exportDate->format('F d, Y g:i A') }}</div>
        <div>Total Logs: {{ $logs ? $logs->count() : 0 }}</div>
        <div>Report ID: EQ-{{ $exportDate->format('Ymd-His') }}</div>
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
        <h3 style="font-size: 11px; font-weight: bold; margin-bottom: 6px;">History Logs</h3>
        <table class="table">
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
                         <td>{{ $equipment->equipment_name ?? 'N/A' }}</td> {{-- Use main equipment --}}
            <td>
                <span class="status-badge status-{{ Str::slug($log->equipment->status ?? $equipment->status ?? 'available') }}">
                    {{ ucfirst($log->equipment->status ?? $equipment->status ?? 'N/A') }}
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
        <div class="footer-grid">
            <div>
                <strong>Total Logs:</strong> {{ $logs ? $logs->count() : 0 }}<br>
                <strong>Equipment ID:</strong> {{ $equipment->id ?? 'N/A' }}
            </div>
            <div>
                <strong>Generated by:</strong> UPITDC Inventory Management System<br>
                <strong>Date:</strong> {{ $exportDate->format('M d, Y') }}
            </div>
            <div>
                <strong>Report Type:</strong> Equipment Log Report<br>
                <strong>Status:</strong> As of {{ $exportDate->format('g:i A') }}
            </div>
        </div>
        <hr>
        <div style="text-align: center;">
            © {{ date('Y') }} University of the Philippines Diliman Information Technology Development Center<br>
            <p>This report was automatically generated by the Inventory Management System</p>
        </div>
    </div>
</body>
</html>