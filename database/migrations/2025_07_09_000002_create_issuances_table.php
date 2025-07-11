<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issuances', function (Blueprint $table) {
            $table->id();
            $table->string('staff_name');
            $table->string('department');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->date('date_issued');
            $table->date('date_returned')->nullable();
            $table->string('pr_number');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issuances');
    }
};