<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('tasksubmissions', function (Blueprint $table) {
            // Only add 'id' if it does not exist
            if (!Schema::hasColumn('tasksubmissions', 'id')) {
                $table->uuid('id')->first();
            }
        });

        // Drop old primary key if exists
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes('tasksubmissions');
        if (isset($indexes['PRIMARY'])) {
            Schema::table('tasksubmissions', function (Blueprint $table) {
                $table->dropPrimary(['task_id', 'user_id']);
            });
        }

        // Make 'id' the primary key if not already
        Schema::table('tasksubmissions', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('tasksubmissions');
            if (!isset($indexes['PRIMARY'])) {
                $table->primary('id');
            }
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::table('tasksubmissions', function (Blueprint $table) {
            if (Schema::hasColumn('tasksubmissions', 'id')) {
                $table->dropPrimary(['id']);
                $table->dropColumn('id');
                $table->primary(['task_id', 'user_id']);
            }
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
