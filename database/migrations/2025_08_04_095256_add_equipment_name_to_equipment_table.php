<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('equipment', function (Blueprint $table) {
        $table->string('equipment_name')->after('department_id');
    });
}

public function down(): void
{
    Schema::table('equipment', function (Blueprint $table) {
        $table->dropColumn('equipment_name');
    });
}
};
