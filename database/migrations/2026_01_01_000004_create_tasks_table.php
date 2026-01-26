<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {   
        if (!Schema::hasTable('tasks')) {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('description');
            $table->date('due_date');
            $table->string('status');
            $table->foreignUuid('council_session_id')->nullable()->constrained('council_sessions')->nullOnDelete()->cascadeOnUpdate();
            // Made nullable and nullOnDelete as per generic best practice if session is deleted, or maybe cascade? 
            // Original migration had 'nullable()->constrained()'. 
            // I'll stick to nullable. 
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tasks')) {
            Schema::dropIfExists('tasks');
        }
    }
};
