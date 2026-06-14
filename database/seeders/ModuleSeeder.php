<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared("
            INSERT INTO `modules` (`id`, `title`, `description`, `created_at`) VALUES
(1, 'Pengantar Digital Marketing', 'Dasar-dasar dan ekosistem digital marketing modern', '2026-05-09 11:43:10'),
(2, 'Search Engine Optimization', 'Teknik SEO on-page, off-page, dan technical', '2026-05-09 11:43:10'),
(3, 'Social Media Marketing', 'Strategi konten dan engagement di media sosial', '2026-05-09 11:43:10'),
(4, 'Pay-Per-Click Advertising', 'Google Ads dan Meta Ads untuk hasil terukur', '2026-05-09 11:43:10'),
(5, 'Content Marketing', 'Membuat dan mendistribusikan konten bernilai', '2026-05-09 11:43:10'),
(6, 'Email Marketing', 'Kampanye email yang efektif dan terukur', '2026-05-09 11:43:10'),
(7, 'Analytics & Data Marketing', 'Menggunakan data untuk keputusan pemasaran cerdas', '2026-05-09 11:43:10');
        ");
    }
}