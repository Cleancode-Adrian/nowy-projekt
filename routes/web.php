<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RatingController as AdminRatingController;
use Illuminate\Support\Facades\Route;

use App\Livewire\HomePage;
use App\Livewire\AnnouncementsList;
use App\Livewire\ShowAnnouncement;
use App\Livewire\AnnouncementProposals;
use App\Livewire\CreateAnnouncement;
use App\Livewire\EditAnnouncement;
use App\Livewire\Dashboard;
use App\Livewire\MyProposals;
use App\Livewire\Messages;
use App\Livewire\ShowMessages;
use App\Livewire\Portfolio;
use App\Livewire\UserProfile;
use App\Livewire\UserRatings;
use App\Livewire\AdvancedSearch;
use App\Livewire\Stats;
use App\Livewire\Notifications;
use App\Livewire\Leaderboard;
use App\Livewire\Blog\BlogIndex;
use App\Livewire\Blog\BlogShow;
use App\Livewire\Faq;
use App\Livewire\PrivacyPolicy;
use App\Livewire\TermsOfService;
use App\Livewire\SavedAnnouncements;
use App\Livewire\ProfileEdit;
use App\Http\Controllers\Admin\BlogController;

// Public pages
Route::get('/', HomePage::class)->name('home');
Route::get('/announcements', AnnouncementsList::class)->name('announcements.index');
Route::get('/announcements/{id}', ShowAnnouncement::class)->name('announcements.show');
Route::get('/szukaj', AdvancedSearch::class)->name('search.advanced');
Route::get('/profil/{user}', UserProfile::class)->name('users.profile');
Route::get('/uzytkownicy/{user}/opinie', UserRatings::class)->name('users.ratings');
Route::get('/ranking', Leaderboard::class)->name('leaderboard');
Route::get('/blog', BlogIndex::class)->name('blog.index');
Route::get('/blog/{slug}', BlogShow::class)->name('blog.show');
Route::get('/faq', Faq::class)->name('faq');
Route::get('/polityka-prywatnosci', PrivacyPolicy::class)->name('privacy-policy');
Route::get('/regulamin', TermsOfService::class)->name('terms-of-service');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard')->with('success', 'Email zweryfikowany pomyślnie!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Link weryfikacyjny wysłany!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Auth routes (Laravel Breeze style)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', [\App\Http\Controllers\Auth\AuthController::class, 'register']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/zapisane', SavedAnnouncements::class)->name('saved.index');
    Route::get('/profil', ProfileEdit::class)->name('profile.edit');
    Route::get('/statystyki', Stats::class)->name('stats');
    Route::get('/powiadomienia', Notifications::class)->name('notifications');

    Route::get('/announcements/create', CreateAnnouncement::class)->name('announcements.create');
    Route::get('/announcements/{announcement}/edit', EditAnnouncement::class)->name('announcements.edit');
    Route::get('/announcements/{announcement}/proposals', AnnouncementProposals::class)->name('announcements.proposals');

    Route::get('/moje-oferty', MyProposals::class)->name('proposals.index');

    Route::get('/wiadomosci', Messages::class)->name('messages.index');
    Route::get('/wiadomosci/{user}', ShowMessages::class)->name('messages.show');

    Route::get('/portfolio', Portfolio::class)->name('portfolio.index');
});

// Admin Login
Route::get('/admin/login', [LoginController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

// Admin Panel Routes (require authentication)
Route::prefix('admin')->name('admin.')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Users Management - Full CRUD
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'delete'])->name('users.delete');

    // Announcements Management
    Route::get('/announcements', [AdminController::class, 'announcements'])->name('announcements');
    Route::get('/announcements/{id}/edit', [AdminController::class, 'editAnnouncement'])->name('announcements.edit');
    Route::post('/announcements/{id}/update', [AdminController::class, 'updateAnnouncement'])->name('announcements.update');
    Route::post('/announcements/{id}/approve', [AdminController::class, 'approveAnnouncement'])->name('announcements.approve');
    Route::post('/announcements/{id}/reject', [AdminController::class, 'rejectAnnouncement'])->name('announcements.reject');

    // Blog Management
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/create', [BlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [BlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{id}/edit', [BlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{id}', [BlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{id}', [BlogController::class, 'delete'])->name('blog.delete');

    // Ratings Moderation
    Route::get('/ratings', [AdminRatingController::class, 'index'])->name('ratings.index');
    Route::post('/ratings/{id}/approve', [AdminRatingController::class, 'approve'])->name('ratings.approve');
    Route::delete('/ratings/{id}/reject', [AdminRatingController::class, 'reject'])->name('ratings.reject');
});
