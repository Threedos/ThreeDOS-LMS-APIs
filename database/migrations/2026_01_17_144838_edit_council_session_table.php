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
       Schema::rename('CouncilSession', 'council_sessions');
       Schema::rename('tasksubmissions', 'task_submissions');
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      
    }
};
