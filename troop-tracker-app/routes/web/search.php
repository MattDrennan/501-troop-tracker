<?php

declare(strict_types=1);

use App\Http\Controllers\Search\CostumeSearchController;
use Illuminate\Support\Facades\Route;

//  SEARCH
Route::prefix('search')
    ->name('search.')
    ->group(function ()
    {
        Route::get('/costumes/{organization}', CostumeSearchController::class)->name('costumes');
    });