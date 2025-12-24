<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\FlashMessageService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class FlashMessageMiddleware
{
    public function __construct(
        protected readonly FlashMessageService $flash
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        View::share('flash', $this->flash);

        return $next($request);
    }
}