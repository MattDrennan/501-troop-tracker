<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Troopers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Trooper;
use App\Services\BreadCrumbService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class TrooperProfileDisplayController
 *
 * Handles the display of a specific trooper's profile page within the admin section.
 * @package App\Http\Controllers\Admin\Troopers
 */
class UpdateController extends Controller
{
    /**
     * TrooperProfileDisplayController constructor.
     *
     * @param BreadCrumbService $crumbs The breadcrumb service for managing navigation history.
     */
    public function __construct(private readonly BreadCrumbService $crumbs)
    {

    }

    /**
     * Handle the incoming request to display the profile page for a specific trooper.
     *
     * Authorizes the user, sets up breadcrumbs for navigation, and returns the
     * trooper profile view with the trooper's data.
     *
     * @param Request $request The incoming HTTP request.
     * @param Trooper $trooper The trooper whose profile is to be displayed.
     * @return View|RedirectResponse The rendered trooper profile view or a redirect response on authorization failure.
     */
    public function __invoke(Request $request, Trooper $trooper): View|RedirectResponse
    {
        $this->authorize('update', $trooper);

        $this->crumbs->addRoute('Command Staff', 'admin.display');
        $this->crumbs->addRoute('Troopers', 'admin.troopers.list');
        $this->crumbs->add('Trooper');

        $organization_authorities = Organization::withAllAssignments($trooper->id)->get();

        $data = [
            'trooper' => $trooper,
            'organization_authorities' => $organization_authorities,
        ];

        return view('pages.admin.troopers.update', $data);
    }
}
