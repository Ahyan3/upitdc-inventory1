<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Department;

class MigrateEquipmentDepartmentData extends Migration
{
    public function up()
    {
        // Create default departments if they don't exist
        $departments = ['ITSG', 'Admin', 'Content Development', 'Software Development', 'Helpdesk', 'Other'];
        
        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept]);
        }

        // Get the default department (ITSG)
        $defaultDept = Department::where('name', 'ITSG')->first();

        if ($defaultDept) {
            // Set all equipment to the default department
            DB::table('equipment')
                ->whereNull('department_id')
                ->update(['department_id' => $defaultDept->id]);
        }
    }

    public function down()
    {
        // Clear all department assignments when rolling back
        DB::table('equipment')->update(['department_id' => null]);
        
        // Optional: Delete the departments
        // DB::table('departments')->truncate();
    }
}