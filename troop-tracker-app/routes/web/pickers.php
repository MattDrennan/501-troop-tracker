<?php

declare(strict_types=1);

use App\Http\Controllers\Pickers\OrganizationPickerController;

//  ACCOUNT
Route::prefix('pickers')
    ->name('pickers.')
    ->middleware('auth')
    ->group(function ()
    {
        Route::get('/organization', OrganizationPickerController::class)->name('organization');
    });
