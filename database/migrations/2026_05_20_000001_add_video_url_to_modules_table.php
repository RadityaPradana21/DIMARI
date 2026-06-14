<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom video_url ke tabel modules.
     * Jalankan: php artisan migrate
     */
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            // Cek dulu agar tidak error kalau sudah ada
            if (!Schema::hasColumn('modules', 'video_url')) {
                $table->string('video_url', 500)->nullable()->after('content')
                      ->comment('URL video YouTube atau file MP4');
            }
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });
    }
};
