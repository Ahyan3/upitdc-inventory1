<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\Issuance;
use App\Models\Request;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
       // Equipment
        $equipment1 = Equipment::create([
            'name' => 'Laptop',
            'model_brand' => 'Dell XPS 13',
            'serial_number' => 'SN123456',
        ]);
        $equipment2 = Equipment::create([
            'name' => 'Monitor',
            'model_brand' => 'LG 27UK850',
            'serial_number' => 'SN789012',
        ]);

        // Issuances
        Issuance::create([
            'staff_name' => 'John Doe',
            'department' => 'IT',
            'equipment_id' => $equipment1->id,
            'date_issued' => '2025-06-01',
            'date_returned' => null,
            'pr_number' => 'PR001',
            'remarks' => 'In use',
        ]);
        Issuance::create([
            'staff_name' => 'Jane Smith',
            'department' => 'HR',
            'equipment_id' => $equipment2->id,
            'date_issued' => '2025-05-15',
            'date_returned' => '2025-07-01',
            'pr_number' => 'PR002',
            'remarks' => 'Returned',
        ]);

        // Requests
        Request::create([
            'staff_name' => 'Alice Johnson',
            'department' => 'Finance',
            'equipment_name' => 'Printer',
            'status' => 'pending',
            ]);
    }
}
