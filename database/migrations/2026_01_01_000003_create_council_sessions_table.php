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
        Schema::create('council_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->datetime('date');
            $table->text('description')->nullable();
            $table->string('material')->nullable();
            $table->foreignUuid('council_id')->constrained('councils')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('council_sessions');
    }
};
