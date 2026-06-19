<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discussion_forums', function (Blueprint $table) {
            if (!Schema::hasColumn('discussion_forums', 'visible')) {
                $table->boolean('visible')->default(true)->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('discussion_forums', function (Blueprint $table) {
            if (Schema::hasColumn('discussion_forums', 'visible')) {
                $table->dropColumn('visible');
            }
        });
    }
};
