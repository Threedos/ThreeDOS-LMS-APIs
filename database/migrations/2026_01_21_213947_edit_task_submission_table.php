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
    Schema::table('task_submissions', function (Blueprint $table) {
        // Drop the old composite primary key
        $table->dropPrimary(['user_id', 'task_id']);

        // Add a new UUID primary key
        $table->uuid('id')->primary()->first(); // 'first()' optional, makes it first column
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_submissions', function (Blueprint $table) {
            //
        });
    }
};
