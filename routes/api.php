<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameRoundController;
use App\Http\Controllers\GameSnapshotController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PlaySessionController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/auth/exchange', [AuthController::class, 'exchange'])->middleware('throttle:10,1');
Route::get('/heroes', [HeroController::class, 'index']);
Route::get('/maps', [MapController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Play Sessions
    Route::apiResource('play-sessions', PlaySessionController::class);
    Route::patch('/play-sessions/{play_session}/end', [PlaySessionController::class, 'end']);

    // Games
    Route::apiResource('games', GameController::class);
    Route::apiResource('games.rounds', GameRoundController::class)
        ->shallow()
        ->only(['store', 'update', 'destroy']);
    Route::put('/games/{game}/snapshots', [GameSnapshotController::class, 'update']);
});
