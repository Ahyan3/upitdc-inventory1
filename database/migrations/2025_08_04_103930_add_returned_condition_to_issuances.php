<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('issuances', function (Blueprint $table) {
            $table->enum('returned_condition', ['good', 'damaged', 'lost'])->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('issuances', function (Blueprint $table) {
            $table->dropColumn('returned_condition');
        });
    }
};
