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
        Schema::table('tasks', function (Blueprint $table) {
            //
              if (Schema::hasColumn('tasks', 'council_id')) {

                // Drop FK FIRST
                $table->dropForeign(['council_id']);

                // Drop index ONLY if it exists
                if (Schema::hasIndex('tasks', 'tasks_council_id_index')) {
                    
                $table->dropIndex(['council_id']);
                }
                // Then drop column
                $table->dropColumn('council_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
        });
    }
};
