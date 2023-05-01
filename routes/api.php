<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Session\StoreController as StoreSession;
use App\Http\Controllers\Session\ShowController as GetSession;
use App\Http\Controllers\Session\IndexController as GetAllSessions;
use App\Http\Controllers\Session\UpdateController as UpdateSession;
use App\Http\Controllers\Game\StoreController as StoreGame;
use App\Http\Controllers\Game\ShowController as GetGame;
use App\Http\Controllers\Game\IndexController as GetAllGames;
use App\Http\Controllers\Game\UpdateController as UpdateGame;
use App\Http\Controllers\Session\DestroyController as DestroySession;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the 'api' middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::get('/logout', [AuthController::class, 'logout'])->middleware('jwt.verify');


// session routes
Route::group(['prefix' => 'battletag/{battletag_id}/session', 'middleware' => ['jwt.verify']], function () {
    Route::get('/', GetAllSessions::class);
    Route::post('/', StoreSession::class);
    Route::get('/{session_id}', GetSession::class);
    Route::put('/{session_id}', UpdateSession::class);
    Route::delete('/{session_id}', DestroySession::class);
});

Route::group(['prefix' => 'battletag/{battletag_id}/session/{session_id}/game', 
'middleware' => ['jwt.verify']], function () {
    Route::get('/', GetAllGames::class);
    Route::post('/', StoreGame::class);
    Route::get('/{game_id}', GetGame::class);
    Route::put('/{game_id}', UpdateGame::class);
});
