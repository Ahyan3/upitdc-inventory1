<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            $table->datetime('requested_at');
            $table->datetime('needed_from');
            $table->datetime('needed_until');
            $table->text('purpose');
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->datetime('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};