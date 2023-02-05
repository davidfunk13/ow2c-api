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

Route::group(['prefix' => '/session', 'middleware' => ['jwt.verify']], function () { 
    Route::post('/', StoreSession::class);
    Route::get('/{id}', GetSession::class);
    Route::get('/', GetAllSessions::class);
    Route::put('/', UpdateSession::class);
});

Route::group(['prefix' => '/game', 'middleware' => ['jwt.verify']], function () { 
    Route::post('/', StoreGame::class);
    Route::get('/{id}', GetGame::class);
    Route::get('/', GetAllGames::class);
    Route::put('/', UpdateGame::class);
});