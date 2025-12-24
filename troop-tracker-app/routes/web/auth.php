<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginDisplayController;
use App\Http\Controllers\Auth\LoginSubmitController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterDisplayController;
use App\Http\Controllers\Auth\RegisterHtmxController;
use App\Http\Controllers\Auth\RegisterSubmitController;
use Illuminate\Support\Facades\Route;

// AUTH
Route::name('auth.')
    ->group(function ()
    {
        Route::get('/login', LoginDisplayController::class)->name('login');
        Route::post('/login', LoginSubmitController::class);
        Route::get('/logout', LogoutController::class)->name('logout');
        Route::get('/register', RegisterDisplayController::class)->name('register');
        Route::post('/register', RegisterSubmitController::class);
        Route::post('/register-htmx/{organization}', RegisterHtmxController::class)->name('register-htmx');
    });