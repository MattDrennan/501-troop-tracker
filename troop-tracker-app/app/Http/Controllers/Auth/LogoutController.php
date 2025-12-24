<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FlashMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Handles user logout.
 */
class LogoutController extends Controller
{
    /**
     * @param FlashMessageService $flash The flash message service.
     */
    public function __construct(
        private readonly FlashMessageService $flash,
    ) {
    }

    /**
     * Handle the incoming request to log the user out.
     *
     * @param Request $request The incoming HTTP request.
     * @return RedirectResponse A redirect response to the login page.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $this->flash->success('You have been logged out.');

        Auth::logout();

        return redirect()->route('auth.login', ['logged_out' => '1']);
    }
}
