<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('council_sessions', function (Blueprint $table) {
            //
                // 1. Drop FK only
        $table->dropForeign(['council_id']);

        // 2. Re-add FK on the EXISTING column
        $table->foreign('council_id')
              ->references('id')
              ->on('councils')
              ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('council_sessions', function (Blueprint $table) {
            //
        });
    }
};
