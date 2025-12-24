<?php

declare(strict_types=1);

use App\Http\Controllers\FaqDisplayController;
use App\Http\Controllers\Widgets\SupportDisplayHtmxController;
use Illuminate\Support\Facades\Route;

Route::get('/faq', FaqDisplayController::class)->name('faq');
Route::get('/support-htmx', SupportDisplayHtmxController::class)->name('support-htmx');