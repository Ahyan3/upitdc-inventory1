<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToIssuancesTable extends Migration
{
    public function up()
    {
        Schema::table('issuances', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable()->after('updated_at');
        });
    }

    public function down()
    {
        Schema::table('issuances', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
}