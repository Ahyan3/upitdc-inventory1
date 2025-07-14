<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixStaffTableDefaults extends Migration
{
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('position')->default('Staff')->change();
            $table->string('password')->default('$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')->change(); // Default hashed 'password'
        });
    }

    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('position')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
}