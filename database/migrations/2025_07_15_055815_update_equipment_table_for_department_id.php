<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEquipmentTableForDepartmentId extends Migration
{
    public function up()
    {
        Schema::table('equipment', function (Blueprint $table) {
            // Remove the 'after' clause since the column might not exist
            $table->unsignedBigInteger('department_id')->nullable();
            
            // Add foreign key constraint separately
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
}