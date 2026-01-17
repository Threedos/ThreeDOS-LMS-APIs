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
        Schema::table('attendances', function (Blueprint $table) {
            //
       
    // Drop foreign key and column ONLY if it exists
    if (Schema::hasColumn('attendances', 'session_id')) {

        // Drop FK
        // Defensive: use try-catch because MySQL doesn't have IF EXISTS for FK
        try {
            $table->dropForeign(['session_id']);
        } catch (\Illuminate\Database\QueryException $e) {
            // FK doesn't exist, ignore
            throw $e;
        }

        $table->dropColumn('session_id');
    }

    // Add new foreign key to council_sessions
    if (!Schema::hasColumn('attendances', 'council_session_id')) {
        $table->foreignUuid('council_session_id')
              ->constrained('councilSession') // corrected table name
              ->cascadeOnDelete();
    }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            //
        });
    }
};
