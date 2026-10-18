<?php

use App\Http\Controllers\Api\ExternalNotificationController;
use App\Http\Middleware\VerifyExternalApiKey;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/external')
    ->middleware(VerifyExternalApiKey::class)
    ->group(function () {
        Route::get('/notifications', [ExternalNotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [ExternalNotificationController::class, 'unreadCount']);
        Route::patch('/notifications/{id}/read', [ExternalNotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-read', [ExternalNotificationController::class, 'markAllAsRead']);
    });
