<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixEquipmentDepartmentRelationship extends Migration
{
    public function up()
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');
        });

        DB::table('equipment')
            ->whereNotNull('department_id')
            ->whereNotIn('department_id', DB::table('departments')->pluck('id'))
            ->update(['department_id' => null]);
    }

    public function down()
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });
    }
}