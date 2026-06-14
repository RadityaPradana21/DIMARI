<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([

            [
                'id' => 1,
                'username' => 'dwi_anggita',
                'email' => 'admin@dimari.id',
                'full_name' => 'Dwi Anggita',
                'phone_number' => null,
                'avatar_url' => null,
                'is_using_custom_avatar' => 0,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'admin',
                'last_login' => null,
                'created_at' => '2026-05-09 11:43:09',
                'email_verified_at' => null,
                'remember_token' => null,
            ],

            [
                'id' => 2,
                'username' => 'nuril_rasyid',
                'email' => 'mentor@dimari.id',
                'full_name' => 'Nuril Rasyid',
                'phone_number' => null,
                'avatar_url' => null,
                'is_using_custom_avatar' => 0,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'mentor',
                'last_login' => null,
                'created_at' => '2026-05-09 11:43:09',
                'email_verified_at' => null,
                'remember_token' => null,
            ],

            [
                'id' => 3,
                'username' => 'rizqi_mubarrok',
                'email' => 'user1@dimari.id',
                'full_name' => 'Rizqi Mubarrok',
                'phone_number' => null,
                'avatar_url' => 'http://localhost/DIMARI2/public/avatars/default/avatar_8.svg',
                'is_using_custom_avatar' => 0,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'user',
                'last_login' => null,
                'created_at' => '2026-05-09 11:43:09',
                'email_verified_at' => null,
                'remember_token' => null,
            ],

            [
                'id' => 4,
                'username' => 'raditya_pradana',
                'email' => 'raditya@gmail.com',
                'full_name' => 'Raditya Pradana',
                'phone_number' => '',
                'avatar_url' => 'http://localhost/DIMARI2/public/avatars/custom/user_4_1778327873.png',
                'is_using_custom_avatar' => 1,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'user',
                'last_login' => null,
                'created_at' => '2026-05-09 11:43:09',
                'email_verified_at' => null,
                'remember_token' => null,
            ],

            [
                'id' => 5,
                'username' => 'budi_santoso',
                'email' => 'budi.santoso@email.com',
                'full_name' => 'Budi Santoso',
                'phone_number' => '08123456847',
                'avatar_url' => null,
                'is_using_custom_avatar' => 0,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'user',
                'last_login' => '2026-05-08 22:57:00',
                'created_at' => '2026-05-09 11:43:09',
                'email_verified_at' => null,
                'remember_token' => null,
            ],

            [
                'id' => 6,
                'username' => 'siti_aminah',
                'email' => 'siti.aminah@email.com',
                'full_name' => 'Siti Aminah',
                'phone_number' => '08123456848',
                'avatar_url' => null,
                'is_using_custom_avatar' => 0,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'user',
                'last_login' => '2026-05-08 22:57:00',
                'created_at' => '2026-05-09 11:43:09',
                'email_verified_at' => null,
                'remember_token' => null,
            ],

            [
                'id' => 7,
                'username' => 'arya_wiguna',
                'email' => 'arya.wiguna@email.com',
                'full_name' => 'Arya Wiguna',
                'phone_number' => '08123456849',
                'avatar_url' => null,
                'is_using_custom_avatar' => 0,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'user',
                'last_login' => '2026-05-08 22:57:00',
                'created_at' => '2026-05-09 11:43:09',
                'email_verified_at' => null,
                'remember_token' => null,
            ],

            [
                'id' => 8,
                'username' => 'dian_sastro',
                'email' => 'dian.sastro@email.com',
                'full_name' => 'Dian Sastro',
                'phone_number' => '08123456850',
                'avatar_url' => null,
                'is_using_custom_avatar' => 0,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                'role' => 'user',
                'last_login' => '2026-05-08 22:57:00',
                'created_at' => '2026-05-09 11:43:09',
                'email_verified_at' => null,
                'remember_token' => null,
            ],

        ]);
    }
}