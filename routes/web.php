<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/punishments/data', 'App\Http\Controllers\DataController@getAllPunishments')->name('bans.all');
