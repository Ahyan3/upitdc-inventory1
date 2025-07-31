<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssueOutsTable extends Migration
{
    public function up()
    {
        Schema::create('issue_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->dateTime('issued_at');
            $table->dateTime('expected_return_at')->nullable();
            $table->dateTime('returned_at')->nullable();
            $table->enum('status', ['in_use', 'maintenance', 'damaged'])->default('in_use');
            $table->text('notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('issue_outs');
    }
}