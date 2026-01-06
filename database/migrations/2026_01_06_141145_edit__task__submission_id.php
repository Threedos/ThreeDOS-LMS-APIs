<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1️⃣ Add new 'id' column (UUID) first
        Schema::table('tasksubmissions', function (Blueprint $table) {
            $table->uuid('id')->first(); // adds new column
        });

        // 2️⃣ Drop old primary key
        Schema::table('tasksubmissions', function (Blueprint $table) {
            $table->dropPrimary(['task_id', 'user_id']);
        });

        // 3️⃣ Make 'id' the primary key
        Schema::table('tasksubmissions', function (Blueprint $table) {
            $table->primary('id');
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('tasksubmissions', function (Blueprint $table) {
            $table->dropPrimary(['id']);
            $table->dropColumn('id');
            $table->primary(['task_id', 'user_id']); // restore old primary key
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
