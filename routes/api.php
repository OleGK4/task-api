<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NoteTagController;
use App\Http\Controllers\TagController;
use App\Http\Resources\NoteCollection;
use App\Models\Note;
use Illuminate\Http\Request;
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


// Authentication
Route::prefix('auth')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Notes interaction
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('notes')->group(function () {
        Route::get('/', [NoteController::class, 'index']);
        Route::post('/', [NoteController::class, 'store']);

        Route::prefix('find')->group(function () {
            Route::get('{search_tag}', [NoteController::class, 'showByTag']); // Search by tag
        });

        Route::prefix('{note}')->group(function () {
            Route::get('/', [NoteController::class, 'show']);
            Route::put('/', [NoteController::class, 'update']);
            Route::delete('/', [NoteController::class, 'destroy']);

            Route::prefix('tags')->group(function () {
                Route::get('/', [NoteTagController::class, 'index']);
                Route::post('/', [NoteTagController::class, 'store']);

                Route::prefix('{tag}')->group(function () {
                    Route::put('/', [NoteTagController::class, 'update']);
                    Route::delete('/', [NoteTagController::class, 'destroy']);
                    Route::get('/', [NoteTagController::class, 'show']);
                });
            });
        });
    });
});


