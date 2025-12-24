<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\FlashMessageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Displays the user registration page.
 */
class RegisterDisplayController extends Controller
{
    /**
     * @param FlashMessageService $flash The flash message service.
     */
    public function __construct(
        private readonly FlashMessageService $flash,
    ) {
    }

    /**
     * Handle the incoming request to display the registration form.
     *
     * @param Request $request The incoming HTTP request.
     * @return View The rendered registration page view.
     */
    public function __invoke(Request $request): View
    {
        $organizations = Organization::fullyLoaded()->get();

        $data = [
            'organizations' => $organizations
        ];

        return view('pages.auth.register', $data);
    }
}
