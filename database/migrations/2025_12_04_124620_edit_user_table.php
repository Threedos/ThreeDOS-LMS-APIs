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
        Schema::table('users', function (Blueprint $table) {
            //
            // $table->string('id')->primary();
            $table->string('access_token')->nullable();
            $table->boolean('revoked')->default(false);
            $table->foreignUuid('role_id')->references('id')->on('roles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('council_id')->references('id')->on('councils')->cascadeOnDelete()->cascadeOnUpdate();
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropForeign(['role_id']);
            $table->dropForeign(['council_id']);
        });
    }
};
