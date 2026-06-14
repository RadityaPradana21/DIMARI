<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForumReply;

class ForumReplySeeder extends Seeder
{
    public function run(): void
    {
        ForumReply::insert([

            [
                'id' => 1,
                'forum_id' => 1,
                'user_id' => 4,
                'content' => 'Pendekatan yang sangat menyegarkan! Pencapaian terbesar saya adalah masuk halaman 1.',
                'parent_reply_id' => null,
                'is_mentor_reply' => 0,
                'likes_count' => 0,
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 2,
                'forum_id' => 1,
                'user_id' => 5,
                'content' => 'Suka dengan sudut pandang positif ini! Saya meningkatkan trafik organik sebesar 40%.',
                'parent_reply_id' => null,
                'is_mentor_reply' => 0,
                'likes_count' => 0,
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 3,
                'forum_id' => 2,
                'user_id' => 6,
                'content' => 'Terima kasih panduannya! Waktu muat web saya turun 2 detik!',
                'parent_reply_id' => null,
                'is_mentor_reply' => 0,
                'likes_count' => 0,
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 4,
                'forum_id' => 4,
                'user_id' => 7,
                'content' => 'Studi kasus adalah emas! Mendatangkan 15 leads berkualitas dalam satu minggu.',
                'parent_reply_id' => null,
                'is_mentor_reply' => 0,
                'likes_count' => 0,
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 5,
                'forum_id' => 4,
                'user_id' => 8,
                'content' => 'Konten video telah mengubah segalanya bagi saya. 3x lebih banyak engagement.',
                'parent_reply_id' => null,
                'is_mentor_reply' => 0,
                'likes_count' => 0,
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

            [
                'id' => 6,
                'forum_id' => 6,
                'user_id' => 2,
                'content' => 'Selamat atas 10k followers-nya! Kemenangan terbesar saya adalah di grup Facebook.',
                'parent_reply_id' => null,
                'is_mentor_reply' => 0,
                'likes_count' => 0,
                'created_at' => '2026-05-09 11:43:10',
                'updated_at' => '2026-05-09 11:43:10',
            ],

        ]);
    }
}