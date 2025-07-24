<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Department;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run()
    {
        $department = Department::firstOrCreate(['name' => 'IT']);
        Equipment::create([
            'staff_name' => 'Test Staff',
            'department_id' => $department->id,
            'equipment_name' => 'Laptop',
            'model_brand' => 'Dell',
            'serial_number' => 'TEST123',
            'pr_number' => 'PR123',
            'date_issued' => now(),
            'status' => 'available',
            'remarks' => 'Test equipment',
        ]);
        Equipment::create([
            'staff_name' => 'Test Staff 2',
            'department_id' => $department->id,
            'equipment_name' => 'Monitor',
            'model_brand' => 'Samsung',
            'serial_number' => 'TEST456',
            'pr_number' => 'PR456',
            'date_issued' => now(),
            'status' => 'issued',
            'remarks' => 'Test monitor',
        ]);
    }
}