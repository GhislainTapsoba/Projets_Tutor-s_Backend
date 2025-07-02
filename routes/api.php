<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Agencies Routes
Route::apiResource('agencies', \App\Http\Controllers\Api\AgencyController::class);
Route::get('/agencies/nearest', [\App\Http\Controllers\Api\AgencyController::class, 'nearest']);

// Tickets Routes
Route::apiResource('tickets', \App\Http\Controllers\Api\TicketController::class);
Route::get('/tickets/stats', [\App\Http\Controllers\Api\TicketController::class, 'statistics']);

// Events Routes
Route::apiResource('events', \App\Http\Controllers\Api\EventController::class);
Route::get('/agencies/{agency}/stats', [\App\Http\Controllers\Api\AgencyController::class, 'statistics']);
Route::get('/events/{event}/stats', [\App\Http\Controllers\Api\EventController::class, 'statistics']);
Route::get('/users/{user}/stats', [\App\Http\Controllers\Api\UserController::class, 'statistics']);
Route::get('/stats', [\App\Http\Controllers\Api\StatisticsController::class, 'index']);
Route::get('/stats/agencies/{agency}', [\App\Http\Controllers\Api\StatisticsController::class, 'agencyStatistics']);
Route::get('/stats/events/{event}', [\App\Http\Controllers\Api\StatisticsController::class, 'eventStatistics']);
Route::get('/stats/users/{user}', [\App\Http\Controllers\Api\StatisticsController::class, 'userStatistics']);

// Users Routes
Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);

// Auth Routes
Route::post('/auth/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/auth/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', [\App\Http\Controllers\Api\AuthController::class, 'user']);

    // Routes pour les utilisateurs (clients)
    Route::middleware('role:client')->group(function () {
        Route::post('/tickets', [\App\Http\Controllers\Api\TicketController::class, 'store']);
        Route::get('/tickets/{ticket}', [\App\Http\Controllers\Api\TicketController::class, 'show']);
        Route::get('/agencies/nearest', [\App\Http\Controllers\Api\AgencyController::class, 'nearest']);
        Route::get('/events', [\App\Http\Controllers\Api\EventController::class, 'index']);
        Route::get('/events/{event}', [\App\Http\Controllers\Api\EventController::class, 'show']);
    });

    // Routes pour les agents
    Route::middleware('role:agent')->group(function () {
        Route::get('/tickets', [\App\Http\Controllers\Api\TicketController::class, 'index']);
        Route::put('/tickets/{ticket}', [\App\Http\Controllers\Api\TicketController::class, 'update']);
        Route::delete('/tickets/{ticket}', [\App\Http\Controllers\Api\TicketController::class, 'destroy']);
        Route::get('/agencies', [\App\Http\Controllers\Api\AgencyController::class, 'index']);
        Route::get('/agencies/{agency}', [\App\Http\Controllers\Api\AgencyController::class, 'show']);
        Route::get('/users/agents', [\App\Http\Controllers\Api\UserController::class, 'index'])->where('role', 'agent');
        Route::get('/users/clients', [\App\Http\Controllers\Api\UserController::class, 'index'])->where('role', 'client');
    });

    // Routes pour les administrateurs
    Route::middleware('role:admin')->group(function () {
        Route::post('/agencies', [\App\Http\Controllers\Api\AgencyController::class, 'store']);
        Route::put('/agencies/{agency}', [\App\Http\Controllers\Api\AgencyController::class, 'update']);
        Route::delete('/agencies/{agency}', [\App\Http\Controllers\Api\AgencyController::class, 'destroy']);
        Route::post('/events', [\App\Http\Controllers\Api\EventController::class, 'store']);
        Route::put('/events/{event}', [\App\Http\Controllers\Api\EventController::class, 'update']);
        Route::delete('/events/{event}', [\App\Http\Controllers\Api\EventController::class, 'destroy']);
        Route::post('/users', [\App\Http\Controllers\Api\UserController::class, 'store']);
        Route::put('/users/{user}', [\App\Http\Controllers\Api\UserController::class, 'update']);
        Route::delete('/users/{user}', [\App\Http\Controllers\Api\UserController::class, 'destroy']);
        Route::get('/users', [\App\Http\Controllers\Api\UserController::class, 'index']);
        Route::get('/users/{user}', [\App\Http\Controllers\Api\UserController::class, 'show']);
    });

    // Routes communes pour tous les rôles
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Api\UserController::class, 'markNotificationAsRead']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\Api\UserController::class, 'markAllNotificationsAsRead']);
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\Api\UserController::class, 'deleteNotification']);
});
