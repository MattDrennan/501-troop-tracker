<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminDisplayController;
use App\Http\Controllers\Admin\Awards\AwardDisplayController;
use Illuminate\Support\Facades\Route;


//  ADMIN
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'check.role:moderator,administrator'])
    ->group(function ()
    {
        Route::get('/', AdminDisplayController::class)->name('display');

        //  ADMIN/AWARDS
        Route::prefix('awards')->name('awards.')->group(function ()
        {
            Route::get('/', AwardDisplayController::class)->name('display');
        });
    });
