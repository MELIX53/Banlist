<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
Route::post('/get_account_data', [\App\Http\Controllers\AuthController::class, 'getAccountData']);

Route::post('/load_file', [\App\Http\Controllers\LoadFileController::class, 'process']);
Route::post('/remove_post', [\App\Http\Controllers\RemovePostController::class, 'process']);
Route::post('/confirmed_post', [\App\Http\Controllers\ConfirmPostController::class, 'process']);
