<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convert week_start ke DATE agar konsisten saat perbandingan
        // module_completions
        if (Schema::hasColumn('module_completions', 'week_start')) {
            DB::statement('ALTER TABLE module_completions MODIFY week_start DATE NOT NULL');
        }

        // quiz_results
        if (Schema::hasColumn('quiz_results', 'week_start')) {
            DB::statement('ALTER TABLE quiz_results MODIFY week_start DATE NOT NULL');
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE module_completions MODIFY week_start DATETIME NOT NULL');
        DB::statement('ALTER TABLE quiz_results MODIFY week_start DATETIME NOT NULL');
    }
};
