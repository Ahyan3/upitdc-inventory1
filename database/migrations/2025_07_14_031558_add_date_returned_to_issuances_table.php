<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateReturnedToIssuancesTable extends Migration
{
    public function up()
    {
        Schema::table('issuances', function (Blueprint $table) {
            $table->timestamp('date_returned')->nullable();
        });
    }

    public function down()
    {
        Schema::table('issuances', function (Blueprint $table) {
            $table->dropColumn('date_returned');
        });
    }
}