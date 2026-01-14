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
        Schema::table('tasksubmissions', function (Blueprint $table) {
            //
            if(Schema::hasColumn('tasksubmissions', 'council_id')) {
                $table->dropForeign(['council_id']);
                $table->dropColumn('council_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumn('tasksubmissions', 'council_id')) {
            Schema::table('tasksubmissions', function (Blueprint $table) {
                //
                  $table->dropForeign(['council_id']);
                $table->dropColumn('council_id');
            });
        }
    }
};
