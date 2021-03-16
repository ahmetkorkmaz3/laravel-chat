<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Broadcast::routes();

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'auth'
], function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::middleware('auth:api')->group(function () {

    Route::prefix('conversations')->group(function () {
        Route::get('', [ConversationController::class, 'index']);
        Route::post('', [ConversationController::class, 'store']);

        Route::prefix('{conversation}')->group(function () {
            Route::get('', [ConversationController::class, 'show']);
            Route::put('', [ConversationController::class, 'update']);
            Route::delete('', [ConversationController::class, 'destroy']);

            Route::prefix('messages')->group(function () {
                Route::get('', [MessageController::class, 'index']);
                Route::post('', [MessageController::class, 'store']);
            });
        });
    });

});
