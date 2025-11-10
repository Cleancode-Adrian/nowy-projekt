<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProposalController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\PortfolioController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/announcements', [AnnouncementController::class, 'index']);
Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);
Route::get('/tags', [TagController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/announcements', [AnnouncementController::class, 'store']);
    Route::get('/my-announcements', [AnnouncementController::class, 'myAnnouncements']);
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);

    // User profile
    Route::post('/profile', [UserController::class, 'updateProfile']);
    Route::post('/profile/change-password', [UserController::class, 'changePassword']);
    Route::delete('/profile/avatar', [UserController::class, 'deleteAvatar']);

    // Saved announcements (bookmarks)
    Route::get('/saved-announcements', [\App\Http\Controllers\Api\SavedAnnouncementController::class, 'index']);
    Route::post('/saved-announcements', [\App\Http\Controllers\Api\SavedAnnouncementController::class, 'store']);
    Route::delete('/saved-announcements/{announcementId}', [\App\Http\Controllers\Api\SavedAnnouncementController::class, 'destroy']);
    Route::get('/saved-announcements/check/{announcementId}', [\App\Http\Controllers\Api\SavedAnnouncementController::class, 'check']);

    // Proposals
    Route::get('/announcements/{announcementId}/proposals', [ProposalController::class, 'index']);
    Route::get('/my-proposals', [ProposalController::class, 'myProposals']);
    Route::post('/proposals', [ProposalController::class, 'store']);
    Route::post('/proposals/{id}/accept', [ProposalController::class, 'accept']);
    Route::post('/proposals/{id}/reject', [ProposalController::class, 'reject']);
    Route::post('/proposals/{id}/withdraw', [ProposalController::class, 'withdraw']);

    // Messages
    Route::get('/conversations', [MessageController::class, 'conversations']);
    Route::get('/messages/{userId}', [MessageController::class, 'show']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead']);
    Route::get('/messages/unread/count', [MessageController::class, 'unreadCount']);

    // Ratings
    Route::post('/ratings', [RatingController::class, 'store']);
    Route::get('/ratings/can-rate/{announcementId}/{userId}', [RatingController::class, 'canRate']);

    // Portfolio
    Route::get('/my-portfolio', [PortfolioController::class, 'myPortfolio']);
    Route::post('/portfolio', [PortfolioController::class, 'store']);
    Route::put('/portfolio/{id}', [PortfolioController::class, 'update']);
    Route::delete('/portfolio/{id}', [PortfolioController::class, 'destroy']);
});

// Public portfolio and ratings
Route::get('/users/{userId}/portfolio', [PortfolioController::class, 'index']);
Route::get('/users/{userId}/ratings', [RatingController::class, 'index']);

