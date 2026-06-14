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
        // Tambahkan kolom baru jika belum ada (melindungi dari migrasi sebelumnya)
        if (!Schema::hasColumn('modules', 'video_title')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->string('video_title', 200)->nullable()->after('content');
            });
        }

        if (!Schema::hasColumn('modules', 'video_description')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->text('video_description')->nullable()->after('video_title');
            });
        }

        if (!Schema::hasColumn('modules', 'video_url')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->string('video_url')->nullable()->after('video_description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus kolom jika masih ada
        if (Schema::hasColumn('modules', 'video_title')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->dropColumn('video_title');
            });
        }

        if (Schema::hasColumn('modules', 'video_description')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->dropColumn('video_description');
            });
        }

        if (Schema::hasColumn('modules', 'video_url')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->dropColumn('video_url');
            });
        }
    }
};
