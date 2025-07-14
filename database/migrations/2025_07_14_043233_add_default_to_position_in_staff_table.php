<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultToPositionInStaffTable extends Migration
{
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('position')->default('Staff')->change();
        });
    }

    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('position')->nullable(false)->change();
        });
    }
}