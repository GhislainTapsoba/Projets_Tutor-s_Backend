<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Routes pour le frontend
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

// Routes pour le frontend React
Route::get('/admin/{any}', function () {
    return view('welcome');
})->where('any', '.*');

// Routes pour le frontend mobile
Route::get('/mobile/{any}', function () {
    return view('welcome');
})->where('any', '.*');
