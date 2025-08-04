<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['department_id']);
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            // Restore the foreign key constraint
            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments')
                  ->onDelete('set null');
        });
    }
};

