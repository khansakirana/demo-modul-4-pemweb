<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// [PENANDA ROUTE API] Didaftarkan via apiResource + parameters('media' => 'media')
Route::apiResource('media', MediaController::class)->parameters([
    'media' => 'media',
]);

Route::apiResource('users', UserController::class);

// [PENANDA ROUTE CUSTOM ACTION] Kelola koleksi media milik pengguna
Route::get('/users/{user}/collection', [UserController::class, 'collection']);
Route::post('/users/{user}/collection', [UserController::class, 'addToCollection']);
Route::delete('/users/{user}/collection/{media}', [UserController::class, 'removeFromCollection']);
