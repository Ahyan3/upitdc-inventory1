<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // e.g., Issued, Returned, Deleted
            $table->string('staff_name');
            $table->string('department');
            $table->string('equipment_name');
            $table->string('details')->nullable(); // e.g., PR Number, Serial
            $table->timestamp('action_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_logs');
    }
};