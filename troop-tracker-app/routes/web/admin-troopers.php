<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\Troopers\ApprovalListController;
use App\Http\Controllers\Admin\Troopers\ApprovalSubmitHtmxController;
use App\Http\Controllers\Admin\Troopers\AuthoritySubmitHtmxController;
use App\Http\Controllers\Admin\Troopers\DenialSubmitHtmxController;
use App\Http\Controllers\Admin\Troopers\ProfileSubmitHtmxController;
use App\Http\Controllers\Admin\Troopers\ListController;
use App\Http\Controllers\Admin\Troopers\UpdateController;
use Illuminate\Support\Facades\Route;


//  ADMIN/TROOPER
Route::prefix('admin/troopers')
    ->name('admin.troopers.')
    ->middleware(['auth', 'check.role:moderator,administrator'])
    ->group(function ()
    {
        Route::get('/', ListController::class)->name('list');
        Route::get('/approvals', ApprovalListController::class)->name('approvals');
        Route::post('/approvals/{trooper}/approve', ApprovalSubmitHtmxController::class)->name('approve-htmx');
        Route::post('/approvals/{trooper}/deny', DenialSubmitHtmxController::class)->name('deny-htmx');
        Route::get('/{trooper}', UpdateController::class)->name('update');
        Route::post('/{trooper}/profile', ProfileSubmitHtmxController::class)->name('profile-htmx');
        Route::post('/{trooper}/authority', AuthoritySubmitHtmxController::class)->name('authority-htmx');
    });
