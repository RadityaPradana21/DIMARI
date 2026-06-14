<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiscussionForum;

class DiscussionForumSeeder extends Seeder
{
    public function run(): void
    {
        DiscussionForum::insert([

            [
                'id' => 1,
                'title' => 'Praktik Terbaik SEO untuk 2024',
                'content' => 'Halo semuanya! Saya ingin memulai diskusi positif tentang strategi SEO apa yang berhasil untuk Anda tahun ini.',
                'author_id' => 1,
                'category' => 'SEO',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 2,
                'title' => 'Meningkatkan Kecepatan Website Sebesar 60%',
                'content' => 'Saya berhasil mengoptimalkan waktu muat website saya. Berikut langkah pastinya: 1. Optimasi gambar, 2. Setup caching.',
                'author_id' => 2,
                'category' => 'SEO',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 3,
                'title' => 'Merayakan Pencapaian SEO',
                'content' => 'Mari buat ruang positif untuk merayakan pencapaian SEO kita! Bagikan kemenangan Anda.',
                'author_id' => 3,
                'category' => 'SEO',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 4,
                'title' => 'Ide Konten yang Benar-benar Menghasilkan Konversi',
                'content' => 'Saya telah melacak jenis konten apa yang paling banyak menghasilkan leads. Studi kasus memberikan hasil terbaik.',
                'author_id' => 4,
                'category' => 'Content',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 5,
                'title' => 'Membangun Kalender Konten yang Efektif',
                'content' => 'Pelajaran penting: pembuatan massal (batching), perencanaan musiman, dan mendengarkan feedback audiens.',
                'author_id' => 5,
                'category' => 'Content',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 6,
                'title' => 'Pencapaian Media Sosial',
                'content' => 'Bulan ini saya mencapai 10k followers di Instagram dan ingin berbagi apa yang berhasil.',
                'author_id' => 6,
                'category' => 'Social',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 7,
                'title' => 'Membangun Komunitas Sejati di Media Sosial',
                'content' => 'Saya telah beralih dari sekadar mencoba viral menjadi membangun koneksi nyata.',
                'author_id' => 7,
                'category' => 'Social',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 8,
                'title' => 'Email Marketing yang Ingin Dibuka Orang',
                'content' => 'Open rate saya naik dari 15% menjadi 45% ketika saya mulai fokus pada konten yang mengutamakan nilai.',
                'author_id' => 8,
                'category' => 'Email',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 9,
                'title' => 'Membangun Email List dengan Hati',
                'content' => 'Saya menumbuhkan daftar kontak dari 100 menjadi 5000 subscriber dalam 6 bulan hanya dengan metode etis.',
                'author_id' => 1,
                'category' => 'Email',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 10,
                'title' => 'Keputusan Berbasis Data Menjadi Kurang Menakutkan',
                'content' => 'Saya dulu terintimidasi oleh analitik, tapi sekarang saya melihatnya sebagai bercerita dengan angka.',
                'author_id' => 2,
                'category' => 'Analytics',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 11,
                'title' => 'Alat Analitik Pilihan Mudah Digunakan',
                'content' => 'Mari berbagi tools analitik yang benar-benar user-friendly. Saya sangat suka laporan baru GA4.',
                'author_id' => 3,
                'category' => 'Analytics',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 12,
                'title' => 'Sisi Manusiawi dari Pemasaran',
                'content' => 'Pada akhirnya, pemasaran adalah tentang terhubung dengan manusia. Bagaimana Anda tetap autentik?',
                'author_id' => 4,
                'category' => 'General',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 13,
                'title' => 'Anggaran Pemasaran yang Efektif',
                'content' => 'Saya melipatgandakan ROI hanya dengan merealokasi 20% dari anggaran saya.',
                'author_id' => 5,
                'category' => 'General',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 14,
                'title' => 'Baru di Dunia Marketing? Thread Sambutan!',
                'content' => 'Jika Anda baru memulai perjalanan pemasaran Anda, ini adalah ruang aman untuk bertanya!',
                'author_id' => 6,
                'category' => 'Community',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 15,
                'title' => 'Peluang Kolaborasi Pemasaran - Mari Terhubung!',
                'content' => 'Saya percaya kolaborasi selalu mengalahkan kompetisi. Mari brainstorming bersama!',
                'author_id' => 7,
                'category' => 'Collaboration',
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

        ]);
    }
}