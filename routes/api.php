<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    UserController,
    EventController,
    AgencyController,
    StatisticsController,
    TicketController,
};

// Route pour récupérer un token CSRF (utile si besoin)
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Routes publiques (pas besoin de token)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Routes protégées par Sanctum (token obligatoire)
Route::middleware('auth:sanctum')->group(function () {

    // Auth: infos utilisateur connecté + logout
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Routes API principales accessibles à tous les utilisateurs authentifiés
    Route::apiResource('events', EventController::class);
    Route::apiResource('agencies', AgencyController::class);
    Route::get('statistics', [StatisticsController::class, 'index']);
    Route::apiResource('tickets', TicketController::class);

    // Routes utilisateur accessibles à tous les rôles authentifiés
    Route::apiResource('users', UserController::class);

    // Routes avec contrôle de rôles spécifiques

    // Routes admin uniquement
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', fn () => response()->json(['message' => 'Bienvenue Admin']));
        // Ajoute ici les routes admin spécifiques si besoin
    });

    // Routes client uniquement
    Route::middleware('role:client')->group(function () {
        Route::get('/client/dashboard', fn () => response()->json(['message' => 'Bienvenue Client']));
        // Ajoute ici les routes clients spécifiques si besoin
    });

    // Routes agent uniquement (exemple)
    Route::middleware('role:agent')->group(function () {
        Route::get('/agent/dashboard', fn () => response()->json(['message' => 'Bienvenue Agent']));
        // Ajoute ici les routes agent spécifiques si besoin
    });
});
