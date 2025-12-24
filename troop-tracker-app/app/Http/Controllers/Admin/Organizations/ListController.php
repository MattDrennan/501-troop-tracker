<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Organizations;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\BreadCrumbService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class ListController
 *
 * Handles the display of the main organizations list in the admin section.
 * This controller fetches and displays a list of organizations with their assignment
 * status for the authenticated user.
 * @package App\Http\Controllers\Admin\Organizations
 */
class ListController extends Controller
{
    /**
     * ListController constructor.
     *
     * @param BreadCrumbService $crumbs The service for managing breadcrumbs.
     */
    public function __construct(private readonly BreadCrumbService $crumbs)
    {
    }

    /**
     * Handle the request to display the organizations list page.
     *
     * Sets up breadcrumbs, retrieves organizations with assignment data for the
     * authenticated user, and returns the list view.
     *
     * @param Request $request The incoming HTTP request object.
     * @return View The rendered organizations list view.
     */
    public function __invoke(Request $request): View
    {
        $this->crumbs->addRoute('Command Staff', 'admin.display');
        $this->crumbs->add('Organizations');

        $organizations = Organization::withAllAssignments(Auth::user()->id)->get();

        $data = [
            'organizations' => $organizations
        ];

        return view('pages.admin.organizations.list', $data);
    }
}
