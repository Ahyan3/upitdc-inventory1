<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('staff_name');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('model_brand')->nullable();
            $table->string('serial_number')->unique();
            $table->string('pr_number')->unique();
            $table->date('date_issued')->nullable();
            $table->enum('status', ['available', 'in_use', 'maintenance', 'damaged'])->default('available');
            $table->string('remarks');
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};