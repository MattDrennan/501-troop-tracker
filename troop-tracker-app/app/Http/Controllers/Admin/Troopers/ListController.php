<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Troopers;

use App\Enums\MembershipRole;
use App\Http\Controllers\Controller;
use App\Models\Trooper;
use App\Services\BreadCrumbService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class TroopersDisplayController
 *
 * Handles the display of the main troopers list in the admin section.
 * This controller fetches and displays a list of troopers, filtering the results
 * based on the authenticated user's role. Administrators see all troopers, while
 * other roles see only the troopers they are assigned to moderate.
 * @package App\Http\Controllers\Admin\Troopers
 */
class ListController extends Controller
{
    /**
     * TroopersDisplayController constructor.
     *
     * @param BreadCrumbService $crumbs The breadcrumb service for managing navigation history.
     */
    public function __construct(private readonly BreadCrumbService $crumbs)
    {

    }

    /**
     * Handle the incoming request to display the troopers list page.
     *
     * Sets up breadcrumbs, retrieves the appropriate list of troopers based on the
     * user's role, and returns the main troopers display view.
     *
     * @param Request $request The incoming HTTP request.
     * @return View|RedirectResponse The rendered dashboard page view or a redirect response.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $this->crumbs->addRoute('Command Staff', 'admin.display');
        $this->crumbs->add('Troopers');

        $troopers = $this->getTroopers();

        $data = [
            'troopers' => $troopers
        ];

        return view('pages.admin.troopers.list', $data);
    }

    /**
     * Get the collection of troopers to be displayed.
     *
     * If the authenticated user is an Administrator, all troopers are returned.
     * Otherwise, it returns only the troopers moderated by the current user.
     * @return Collection
     */
    private function getTroopers(): Collection
    {
        $trooper = Auth::user();

        if ($trooper->membership_role == MembershipRole::Administrator)
        {
            return Trooper::orderBy(Trooper::NAME)->get();
        }

        return Trooper::moderatedBy($trooper)->orderBy(Trooper::NAME)->get();
    }
}
