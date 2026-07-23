<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('council_sessions')) {
            Schema::create('council_sessions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->date('date')->nullable();
                $table->text('description')->nullable();
                $table->string('material')->nullable();

                $table->foreignUuid('council_id')
                      ->nullable()
                      ->constrained('councils')
                      ->nullOnDelete()
                      ->cascadeOnUpdate();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('council_sessions')) {
            Schema::dropIfExists('council_sessions');
        }
    }
};
