<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\AbsenceController;
use App\Http\Controllers\Api\V1\NoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes V1
|--------------------------------------------------------------------------
|
| Routes API version 1 pour EduSmartCM
| Toutes les routes sont protégées par l'authentification Sanctum
|
*/

// Routes d'authentification (non protégées)
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    // Routes d'authentification (protégées)
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    
    Route::prefix('v1')->group(function () {
        // Routes pour les notes
        Route::apiResource('notes', NoteController::class);
        
        // Routes pour les absences
        Route::apiResource('absences', AbsenceController::class);
        
        // Route supplémentaire pour la mise à jour du statut d'une absence
        Route::patch('absences/{absence}/statut', [AbsenceController::class, 'updateStatut'])
            ->name('absences.update-statut');
    });
});