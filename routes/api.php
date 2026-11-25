<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// CRUD API lengkap (index, show, store, update, destroy) dengan status code tepat
Route::apiResource('media', MediaController::class)->parameters([
    'media' => 'media',
]);

Route::apiResource('users', UserController::class);

// Custom action di luar CRUD standar (attach/detach relasi)
Route::get('/users/{user}/collection', [UserController::class, 'collection']);
Route::post('/users/{user}/collection', [UserController::class, 'addToCollection']);
Route::delete('/users/{user}/collection/{media}', [UserController::class, 'removeFromCollection']);
