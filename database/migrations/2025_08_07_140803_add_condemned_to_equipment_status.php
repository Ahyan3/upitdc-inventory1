<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCondemnedToEquipmentStatus extends Migration
{
    public function up()
    {
        Schema::table('equipment', function (Blueprint $table) {
            // Update the enum column to include 'condemned'
            DB::statement("ALTER TABLE equipment MODIFY COLUMN status ENUM('available', 'in_use', 'maintenance', 'damaged', 'condemned') NOT NULL");
        });
    }

    public function down()
    {
        // Revert to original enum values
        Schema::table('equipment', function (Blueprint $table) {
            DB::statement("ALTER TABLE equipment MODIFY COLUMN status ENUM('available', 'in_use', 'maintenance', 'damaged') NOT NULL");
        });
    }
}