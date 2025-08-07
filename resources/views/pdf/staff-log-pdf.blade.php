<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9px;
            color: #333;
            padding: 20px;
        }

        h1 {
            font-size: 18px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 6px;
            color: #dc3545;
        }

        h2 {
            font-size: 14px;
            text-align: center;
            margin-bottom: 4px;
        }

        h3 {
            font-size: 11px;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 6px;
        }

        p {
            font-size: 8px;
            text-align: center;
            margin-bottom: 10px;
            color: #555;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th, .table td {
            border: 1px solid #ccc;
            padding: 6px 4px;
            font-size: 8px;
        }

        .table th {
            background-color: #dc3545;
            color: white;
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            border-top: 1px solid #ddd;
            font-size: 8px;
            color: #666;
            padding-top: 8px;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <h1>University of the Philippines ITDC</h1>
    <h2>{{ $title }}</h2>
    <p>Generated on: {{ $exportDate->format('F d, Y g:i A') }}</p>

    <!-- Staff Info -->
    <h3>Staff Details</h3>
    <table class="table">
        <tr>
            <th>Name</th>
            <th>Department</th>
            <th>Email</th>
        </tr>
        <tr>
            <td>{{ $staff->name }}</td>
            <td>{{ $staff->department ?? 'N/A' }}</td>
            <td>{{ $staff->email ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- Logs -->
    <h3>History Logs</h3>
    @if ($logs->isEmpty())
        <p>No logs available for this staff member.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 20%;">Date</th>
                    <th style="width: 12%;">Action</th>
                    <th style="width: 20%;">Model</th>
                    <th style="width: 20%;">Changes</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($log->date)->format('F d, Y g:i A') }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->model }}</td>
                    <td>{{ $log->changes }}</td>
                    <td>{{ $log->description }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        © {{ date('Y') }} University of the Philippines Diliman Information Technology Development Center<br>
    </div>

</body>
</html>