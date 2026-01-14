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
            
            $table->foreignUuid('CouncilSession_id')->nullable()->constrained('CouncilSession');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if(Schema::hasColumn('tasks', 'CouncilSession_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                //
                $table->dropColumn('CouncilSession_id');
            });
        }
    }
};
