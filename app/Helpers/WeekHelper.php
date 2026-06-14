<?php
namespace App\Helpers;

use App\Models\QuizResult;
use App\Models\ModuleCompletion;

class WeekHelper
{
    /**
     * Mengembalikan tanggal Senin minggu ini dalam format Y-m-d
     * (tanpa jam, agar konsisten saat dibandingkan di DB)
     */
    public static function thisWeekMonday(): string
    {
        return now()->startOfWeek()->format('Y-m-d');
    }

    public static function hasQuizThisWeek(int $userId, int $moduleId): bool
    {
        return QuizResult::where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->where('week_start', self::thisWeekMonday())
            ->exists();
    }

    public static function getQuizResult(int $userId, int $moduleId)
    {
        return QuizResult::where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->where('week_start', self::thisWeekMonday())
            ->first();
    }

    public static function hasCompletedModule(int $userId, int $moduleId): bool
    {
        return ModuleCompletion::where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->whereDate('week_start', self::thisWeekMonday())
            ->exists();
    }
}
