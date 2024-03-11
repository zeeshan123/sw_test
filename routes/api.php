<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CharacterController;
use App\Http\Controllers\API\FilmController;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);



Route::middleware('auth:api')->group( function () {

    Route::resource('films', FilmController::class);
    Route::get('/film-search', [FilmController::class, 'searchFilm']);
    Route::post('/film-update', [FilmController::class, 'updateFilm']);
    Route::get('/film/{id}', [FilmController::class, 'showById']);
    Route::post('/film/{id}', [FilmController::class, 'deleteById']);

    Route::resource('characters', CharacterController::class);
    Route::get('/character-search', [CharacterController::class, 'searchCharacter']);
    Route::post('/character-update', [CharacterController::class, 'updateCharacter']);
    Route::post('/character/{id}', [CharacterController::class, 'deleteById']);
   


});