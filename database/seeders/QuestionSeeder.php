<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared("
            INSERT INTO `questions` (`id`, `module_id`, `question_text`, `created_at`) VALUES
(1, 1, 'Apa yang dimaksud dengan digital marketing?', '2026-05-09 11:43:11'),
(2, 1, 'Model AIDA dalam digital marketing singkatan dari?', '2026-05-09 11:43:11'),
(3, 1, 'Manakah yang BUKAN merupakan keunggulan digital marketing dibanding media konvensional?', '2026-05-09 11:43:11'),
(4, 1, 'Platform apa yang termasuk dalam ekosistem digital marketing?', '2026-05-09 11:43:11'),
(5, 1, 'Apa yang dimaksud dengan targeting dalam digital marketing?', '2026-05-09 11:43:11'),
(6, 1, 'Apa tujuan utama dari brand awareness?', '2026-05-09 11:43:11'),
(7, 1, 'Tahap manakah dalam funnel pemasaran yang berkaitan dengan konversi pembelian?', '2026-05-09 11:43:11'),
(8, 1, 'Salah satu ciri khas digital marketing dibanding pemasaran tradisional adalah?', '2026-05-09 11:43:11'),
(9, 1, 'Manakah yang merupakan contoh owned media dalam digital marketing?', '2026-05-09 11:43:11'),
(10, 1, 'ROI dalam konteks digital marketing berarti?', '2026-05-09 11:43:11'),
(11, 2, 'SEO adalah singkatan dari?', '2026-05-09 11:43:11'),
(12, 2, 'Manakah yang termasuk dalam On-Page SEO?', '2026-05-09 11:43:11'),
(13, 2, 'Apa yang dimaksud dengan backlink dalam konteks SEO?', '2026-05-09 11:43:11'),
(14, 2, 'Google menggunakan berapa faktor untuk menentukan ranking website?', '2026-05-09 11:43:11'),
(15, 2, 'Apa kepanjangan dari E-E-A-T dalam panduan Google?', '2026-05-09 11:43:11'),
(16, 2, 'Core Web Vitals mengukur aspek apa dari sebuah website?', '2026-05-09 11:43:11'),
(17, 2, 'Manakah tool yang TIDAK digunakan untuk riset kata kunci?', '2026-05-09 11:43:11'),
(18, 2, 'Apa fungsi dari sitemap.xml dalam SEO?', '2026-05-09 11:43:11'),
(19, 2, 'Manakah praktik yang termasuk Black Hat SEO?', '2026-05-09 11:43:11'),
(20, 2, 'Berapa lama umumnya hasil SEO mulai terlihat secara signifikan?', '2026-05-09 11:43:11'),
(21, 3, 'Platform media sosial mana yang paling dominan di kalangan anak muda Indonesia untuk konten video pendek?', '2026-05-09 11:43:11'),
(22, 3, 'Rasio konten yang direkomendasikan antara edukasi, hiburan, inspirasi, dan promosi adalah?', '2026-05-09 11:43:11'),
(23, 3, 'Apa yang dimaksud dengan FYP pada TikTok?', '2026-05-09 11:43:11'),
(24, 3, 'Content pillar yang berfokus pada penjualan langsung sebaiknya berapa persen dari total konten?', '2026-05-09 11:43:11'),
(25, 3, 'Apa itu engagement rate dalam social media marketing?', '2026-05-09 11:43:11'),
(26, 3, 'Manakah yang BUKAN termasuk format iklan di Meta (Facebook/Instagram)?', '2026-05-09 11:43:11'),
(27, 3, 'Jam prime time posting di media sosial umumnya adalah?', '2026-05-09 11:43:11'),
(28, 3, 'Apa fungsi call-to-action (CTA) dalam konten media sosial?', '2026-05-09 11:43:11'),
(29, 3, 'Manakah yang termasuk paid media di media sosial?', '2026-05-09 11:43:11'),
(30, 3, 'Apa yang dimaksud dengan social proof dalam pemasaran?', '2026-05-09 11:43:11'),
(31, 4, 'PPC adalah singkatan dari?', '2026-05-09 11:43:11'),
(32, 4, 'Manakah yang termasuk format Google Ads?', '2026-05-09 11:43:11'),
(33, 4, 'CTR dalam PPC advertising berarti?', '2026-05-09 11:43:11'),
(34, 4, 'ROAS adalah metrik yang mengukur?', '2026-05-09 11:43:11'),
(35, 4, 'Apa yang dimaksud dengan Quality Score di Google Ads?', '2026-05-09 11:43:11'),
(36, 4, 'Manakah yang BUKAN format iklan di Meta Ads?', '2026-05-09 11:43:11'),
(37, 4, 'CPA dalam PPC advertising singkatan dari?', '2026-05-09 11:43:11'),
(38, 4, 'Remarketing dalam digital advertising bertujuan untuk?', '2026-05-09 11:43:11'),
(39, 4, 'Faktor apa yang mempengaruhi posisi iklan di Google Search?', '2026-05-09 11:43:11'),
(40, 4, 'Manakah strategi bidding yang tepat untuk memaksimalkan konversi dengan budget tetap?', '2026-05-09 11:43:11'),
(41, 5, 'Apa definisi utama content marketing?', '2026-05-09 11:43:11'),
(42, 5, 'Manakah yang termasuk jenis konten dalam content marketing?', '2026-05-09 11:43:11'),
(43, 5, 'Framework storytelling yang sering digunakan dalam content marketing adalah?', '2026-05-09 11:43:11'),
(44, 5, 'Apa fungsi content calendar?', '2026-05-09 11:43:11'),
(45, 5, 'Manakah yang merupakan contoh lead magnet?', '2026-05-09 11:43:11'),
(46, 5, 'Dalam content marketing, mana yang lebih penting?', '2026-05-09 11:43:11'),
(47, 5, 'Tool yang dapat digunakan untuk manajemen content calendar adalah?', '2026-05-09 11:43:11'),
(48, 5, 'Apa yang dimaksud dengan evergreen content?', '2026-05-09 11:43:11'),
(49, 5, 'Manakah yang bukan merupakan KPI content marketing?', '2026-05-09 11:43:11'),
(50, 5, 'Tujuan utama dari content marketing bagi bisnis adalah?', '2026-05-09 11:43:11'),
(51, 6, 'Rata-rata ROI email marketing menurut DMA adalah?', '2026-05-09 11:43:11'),
(52, 6, 'Apa yang dimaksud dengan open rate dalam email marketing?', '2026-05-09 11:43:11'),
(53, 6, 'Manakah yang merupakan cara efektif membangun email list?', '2026-05-09 11:43:11'),
(54, 6, 'Jenis email apakah yang dikirim secara otomatis setelah seseorang mendaftar?', '2026-05-09 11:43:11'),
(55, 6, 'Rata-rata open rate industri email marketing adalah?', '2026-05-09 11:43:11'),
(56, 6, 'Personalisasi nama penerima di subject line dapat meningkatkan open rate hingga?', '2026-05-09 11:43:11'),
(57, 6, 'Apa yang dimaksud dengan A/B testing dalam email marketing?', '2026-05-09 11:43:11'),
(58, 6, 'Manakah yang BUKAN merupakan jenis email campaign?', '2026-05-09 11:43:11'),
(59, 6, 'Abandoned cart email dikirim kepada?', '2026-05-09 11:43:11'),
(60, 6, 'Apa praktik terbaik untuk menghindari email masuk ke folder spam?', '2026-05-09 11:43:11'),
(61, 7, 'Apa kepanjangan dari KPI dalam konteks digital marketing?', '2026-05-09 11:43:11'),
(62, 7, 'Google Analytics 4 (GA4) berbasis pada sistem tracking apa?', '2026-05-09 11:43:11'),
(63, 7, 'Apa yang dimaksud dengan bounce rate?', '2026-05-09 11:43:11'),
(64, 7, 'CAC dalam marketing analytics berarti?', '2026-05-09 11:43:11'),
(65, 7, 'LTV atau CLV dalam konteks bisnis digital berarti?', '2026-05-09 11:43:11'),
(66, 7, 'Apa yang dimaksud dengan attribution model dalam digital marketing?', '2026-05-09 11:43:11'),
(67, 7, 'Tool visualisasi data yang dapat digunakan untuk marketing dashboard adalah?', '2026-05-09 11:43:11'),
(68, 7, 'Apa itu funnel analysis dalam Google Analytics?', '2026-05-09 11:43:11'),
(69, 7, 'SMART dalam konteks penetapan KPI marketing berarti?', '2026-05-09 11:43:11'),
(70, 7, 'Manakah yang bukan merupakan metrik utama dalam data-driven marketing?', '2026-05-09 11:43:11');
        ");
    }
}