<?php

declare(strict_types=1);

use App\Http\Controllers\Dashboard\AwardsHtmxController;
use App\Http\Controllers\Dashboard\DashboardDisplayController;
use App\Http\Controllers\Dashboard\DonationsHtmxController;
use App\Http\Controllers\Dashboard\HistoricalTroopsHtmxController;
use App\Http\Controllers\Dashboard\TaggedUploadsHtmxController;
use App\Http\Controllers\Dashboard\UpcomingTroopsHtmxController;
use Illuminate\Support\Facades\Route;

//  DASHBOARD
Route::prefix('dashboard')
    ->name('dashboard.')
    ->middleware('auth')
    ->group(function ()
    {
        Route::get('/', DashboardDisplayController::class)->name('display');
        Route::get('/upcoming-troops-htmx', UpcomingTroopsHtmxController::class)->name('upcoming-troops-htmx');
        Route::get('/historical-troops-htmx', HistoricalTroopsHtmxController::class)->name('historical-troops-htmx');
        Route::get('/donations-htmx', DonationsHtmxController::class)->name('donations-htmx');
        Route::get('/awards-htmx', AwardsHtmxController::class)->name('awards-htmx');
        Route::get('/tagged-uploads-htmx', TaggedUploadsHtmxController::class)->name('tagged-uploads-htmx');
    });