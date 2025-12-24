<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\Settings\ListController;
use App\Http\Controllers\Admin\Settings\UpdateHtmxController;
use Illuminate\Support\Facades\Route;

//  ADMIN/SETTINGS
Route::prefix('admin/settings')
    ->name('admin.settings.')
    ->middleware(['auth', 'check.role:administrator'])
    ->group(function ()
    {
        Route::get('/', ListController::class)->name('list');
        Route::post('/{setting}', UpdateHtmxController::class)->name('update-htmx');
    });
