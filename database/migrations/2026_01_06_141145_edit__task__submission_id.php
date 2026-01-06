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
        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('tasksubmissions', function (Blueprint $table) {

            // Make it the primary key
            $table->uuid('id')->first()->primary();
        });

        // Optional: drop old composite primary key columns if needed
        Schema::table('tasksubmissions', function (Blueprint $table) {
            $table->dropColumn(['task_id', 'user_id']); // only if safe
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('tasksubmissions', function (Blueprint $table) {
            // Recreate old columns
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');

            // Restore composite primary key
            $table->primary(['task_id', 'user_id']);

            // Drop the UUID primary
            $table->dropColumn('id');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
