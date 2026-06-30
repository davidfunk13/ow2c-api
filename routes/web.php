<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'OWC API']);
});

Route::get('/auth/battlenet/redirect', [AuthController::class, 'redirect']);
Route::get('/auth/battlenet/callback', [AuthController::class, 'callback']);
