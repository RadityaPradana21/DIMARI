<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'email',
        'full_name',
        'phone_number',
        'avatar_url',
        'is_using_custom_avatar',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Tabel users hanya punya created_at, tidak ada updated_at
    const UPDATED_AT = null;

    protected $casts = [
        'email_verified_at'      => 'datetime',
        'is_using_custom_avatar' => 'boolean',
    ];

    // ── Relasi ────────────────────────────────────────────

    public function quizResults()
    {
        return $this->hasMany(QuizResult::class);
    }

    public function moduleCompletions()
    {
        return $this->hasMany(ModuleCompletion::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    public function forums()
    {
        return $this->hasMany(DiscussionForum::class, 'author_id');
    }

    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function analytic()
    {
        return $this->hasOne(UserAnalytic::class);
    }

    // ── Accessors ─────────────────────────────────────────

    public function getNameAttribute(): string
    {
        return $this->full_name ?? $this->username;
    }

    /**
     * Mengembalikan URL avatar siap pakai.
     *
     * avatar_url di DB bisa berisi:
     *  - Full URL  : "http://localhost/public/avatars/default/avatar_1.svg" (data lama DIMARI2)
     *  - Path relatif custom : "avatars/custom/user_1_1234567890.jpg" (upload Laravel)
     *  - NULL      : belum set avatar
     *
     * is_using_custom_avatar = true  → upload custom, ada di storage/app/public/
     * is_using_custom_avatar = false → avatar default dari public/avatars/default/
     */
    public function getAvatarAttribute(): string
    {
        if ($this->avatar_url) {
            // Custom upload — disimpan di storage Laravel
            if ($this->is_using_custom_avatar) {
                // Sudah full URL? kembalikan langsung
                if (str_starts_with($this->avatar_url, 'http')) {
                    return $this->avatar_url;
                }
                return asset('storage/' . $this->avatar_url);
            }

            // Avatar default
            // Kalau sudah berupa full URL (dari data lama PHP native), kembalikan langsung
            if (str_starts_with($this->avatar_url, 'http')) {
                return $this->avatar_url;
            }

            // Format baru: hanya nomor "1" s/d "10"
            if (is_numeric(trim($this->avatar_url))) {
                return asset('avatars/default/avatar_' . trim($this->avatar_url) . '.svg');
            }

            // Format nama file: "avatar_3.svg" atau "avatar_3.png"
            return asset('avatars/default/' . basename($this->avatar_url));
        }

        // Tidak ada avatar → generate dari inisial nama
        return 'https://ui-avatars.com/api/?name='
            . urlencode($this->full_name ?? $this->username)
            . '&background=8b5cf6&color=fff&bold=true&size=128';
    }
}
