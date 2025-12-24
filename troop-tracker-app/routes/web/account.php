<?php

declare(strict_types=1);

use App\Http\Controllers\Account\CostumesDeleteHtmxController;
use App\Http\Controllers\Account\CostumesListHtmxController;
use App\Http\Controllers\Account\CostumesSubmitHtmxController;
use App\Http\Controllers\Account\NotificationsListHtmxController;
use App\Http\Controllers\Account\NotificationsSubmitHtmxController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\ProfileSubmitHtmxController;
use Illuminate\Support\Facades\Route;

//  ACCOUNT
Route::prefix('account')
    ->name('account.')
    ->middleware('auth')
    ->group(function ()
    {
        Route::get('/', ProfileController::class)->name('display');
        Route::post('/profile-htmx', ProfileSubmitHtmxController::class)->name('profile-htmx');
        Route::get('/notifications-htmx', NotificationsListHtmxController::class)->name('notifications-htmx');
        Route::post('/notifications-htmx', NotificationsSubmitHtmxController::class);
        Route::get('/costumes-htmx', CostumesListHtmxController::class)->name('costumes-htmx');
        Route::post('/costumes-htmx', CostumesSubmitHtmxController::class);
        Route::delete('/costumes-htmx', CostumesDeleteHtmxController::class);
    });
