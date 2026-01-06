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

        // Only add 'id' column if it doesn't exist
        if (!Schema::hasColumn('tasksubmissions', 'id')) {
            Schema::table('tasksubmissions', function (Blueprint $table) {
                $table->uuid('id')->first();
            });
        }

        // Drop old primary key safely
        DB::statement('ALTER TABLE tasksubmissions DROP PRIMARY KEY');

        // Make 'id' the primary key
        DB::statement('ALTER TABLE tasksubmissions ADD PRIMARY KEY (id)');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::statement('ALTER TABLE tasksubmissions DROP PRIMARY KEY');
        DB::statement('ALTER TABLE tasksubmissions ADD PRIMARY KEY (task_id, user_id)');
        DB::statement('ALTER TABLE tasksubmissions DROP COLUMN id');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
