<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE `issuances` MODIFY `status` ENUM('available', 'in_use', 'maintenance', 'damage', 'returned') NOT NULL");
        DB::statement("ALTER TABLE `issuances` MODIFY `returned_condition` ENUM('good', 'damaged', 'lost') NULL");

        DB::statement("ALTER TABLE `equipment` MODIFY `returned_condition` ENUM('good', 'damaged', 'lost') NULL");
    }

    public function down(): void
    {
        // If you want to revert
        DB::statement("ALTER TABLE `issuances` MODIFY `status` ENUM('available', 'in_use', 'maintenance', 'damage') NOT NULL");
        DB::statement("ALTER TABLE `issuances` MODIFY `returned_condition` VARCHAR(255) NULL");

        DB::statement("ALTER TABLE `equipment` MODIFY `returned_condition` VARCHAR(255) NULL");
    }
};
