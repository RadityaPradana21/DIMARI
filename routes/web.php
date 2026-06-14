<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ModuleManagementController as AdminModuleController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\QuizManagementController as AdminQuizController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;

use App\Http\Controllers\Mentor\DashboardController as MentorDashboardController;
use App\Http\Controllers\Mentor\ModuleController as MentorModuleActionController;
use App\Http\Controllers\Mentor\QuizManagementController as MentorQuizController;

Route::get('/', fn() => redirect()->route('login'));

// ── User (Student) routes ─────────────────────────────────────
Route::middleware(['auth', 'role:user'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Materi
    Route::get('/materi', [MateriController::class, 'index'])->name('materi');
    Route::post('/materi/complete', [MateriController::class, 'complete'])->name('materi.complete');

    // Video
    Route::get('/video', [VideoController::class, 'index'])->name('video');

    // Quiz
    Route::get('/quiz', [QuizController::class, 'index'])->name('quiz');
    Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');

    // Achievements (halaman terpisah)
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/avatar/upload', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
    Route::post('/profile/avatar/reset', [ProfileController::class, 'resetAvatar'])->name('profile.avatar.reset');
});

// ── Forum routes (accessible by all authenticated users) ─────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/forum', [ForumController::class, 'index'])->name('forum');
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::post('/forum/{forum}/reply', [ForumController::class, 'reply'])->name('forum.reply');
    Route::get('/forum/{forum}/edit', [ForumController::class, 'edit'])->name('forum.edit');
    Route::put('/forum/{forum}', [ForumController::class, 'update'])->name('forum.update');
    Route::delete('/forum/{forum}', [ForumController::class, 'destroy'])->name('forum.destroy');
    Route::get('/forum/reply/{reply}/edit', [ForumController::class, 'editReply'])->name('forum.reply.edit');
    Route::put('/forum/reply/{reply}', [ForumController::class, 'updateReply'])->name('forum.reply.update');
    Route::delete('/forum/reply/{reply}', [ForumController::class, 'destroyReply'])->name('forum.reply.destroy');
});

// ── Mentor routes ─────────────────────────────────────────────
Route::middleware(['auth', 'role:mentor'])
    ->prefix('mentor')
    ->name('mentor.')
    ->group(function () {
        Route::get('/', [MentorDashboardController::class, 'index'])->name('index');

        // CRUD Soal
        Route::post('/quizzes', [MentorQuizController::class, 'store'])->name('quizzes.store');
        Route::put('/quizzes/{quiz}', [MentorQuizController::class, 'update'])->name('quizzes.update');
        Route::delete('/quizzes/{quiz}', [MentorQuizController::class, 'destroy'])->name('quizzes.destroy');

        // CRUD Modul
        Route::post('/modules', [MentorModuleActionController::class, 'store'])->name('modules.store');
        Route::put('/modules/{module}', [MentorModuleActionController::class, 'update'])->name('modules.update');
        Route::delete('/modules/{module}', [MentorModuleActionController::class, 'destroy'])->name('modules.destroy');
    });

// ── Admin routes ──────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('index');
        Route::resource('users', UserManagementController::class);
        Route::resource('modules', AdminModuleController::class);
        Route::resource('quizzes', AdminQuizController::class);
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
    });

// ── Profil (Breeze patch/delete) ──────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.patch');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── API / Analytics ───────────────────────────────────────────
Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('/track/interaction', [AnalyticsController::class, 'trackInteraction']);
    Route::post('/track/page', [AnalyticsController::class, 'trackPageVisit']);
    Route::post('/learning-time', [AnalyticsController::class, 'updateLearningTime']);
});

require __DIR__.'/auth.php';
