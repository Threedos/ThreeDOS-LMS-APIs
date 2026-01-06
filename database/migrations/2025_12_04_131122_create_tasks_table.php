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
        Schema::create('tasksubmissions', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();
            // Foreign keys
            $table->foreignUuid('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreignUuid('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('file');
            $table->string('status')->default('Submitted');
            $table->string('grade')->nullable();
            $table->string('comment')->nullable();
            $table->foreignUuid('council_id')->references('id')->on('councils')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasksubmissions');
    }
};
