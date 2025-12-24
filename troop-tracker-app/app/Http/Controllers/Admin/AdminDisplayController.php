<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trooper;
use App\Services\FlashMessageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Handles the display of the main administration dashboard.
 *
 * This controller provides a summary of administrative tasks, such as displaying
 * the count of troopers pending approval and setting a relevant flash message.
 */
class AdminDisplayController extends Controller
{
    /**
     * Creates a new AdminDisplayController instance.
     *
     * @param FlashMessageService $flash The flash message service.
     */
    public function __construct(
        private readonly FlashMessageService $flash,
    ) {
    }

    /**
     * Handle the incoming request to display the admin dashboard.
     *
     * It calculates the number of troopers pending approval, sets a corresponding
     * flash message, and renders the main admin view.
     *
     * @return View|RedirectResponse The rendered admin dashboard view or a redirect response.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $not_approved = Trooper::pendingApprovals()->count();

        if ($not_approved == 1)
        {
            $msg = "There is {$not_approved} trooper ready for action!";
        }
        elseif ($not_approved > 1)
        {
            $msg = "There are {$not_approved} troopers ready for action!";
        }

        if ($not_approved > 0)
        {
            $this->flash->warning($msg);
        }

        $data = ['not_approved' => $not_approved];

        return view('pages.admin.display', $data);
    }
}
