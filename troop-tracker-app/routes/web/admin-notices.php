<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\Notices\CreateController;
use App\Http\Controllers\Admin\Notices\CreateSubmitController;
use App\Http\Controllers\Admin\Notices\ListController;
use App\Http\Controllers\Admin\Notices\UpdateController;
use App\Http\Controllers\Admin\Notices\UpdateSubmitController;
use Illuminate\Support\Facades\Route;

//  ADMIN/ORGANIZATIONS
Route::prefix('admin/notices')
    ->name('admin.notices.')
    ->middleware(['auth', 'check.role:moderator,administrator'])
    ->group(function ()
    {
        Route::get('/', ListController::class)->name('list');
        Route::get('/create', CreateController::class)->name('create');
        Route::post('/create', CreateSubmitController::class);
        Route::get('/{notice}/update', UpdateController::class)->name('update');
        Route::post('/{notice}/update', UpdateSubmitController::class);
    });
