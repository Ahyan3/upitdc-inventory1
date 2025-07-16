<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            'ITSG',
            'Admin',
            'Content Development',
            'Software Development',
            'Helpdesk',
            'Other'
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['name' => $department]);
        }
    }
}