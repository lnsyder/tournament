<?php

use App\Http\Controllers\MatchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('/')->group(function () {
    Route::post('play-matches', [MatchController::class, 'playMatches']);
    Route::get('reset', [MatchController::class, 'reset']);
});
