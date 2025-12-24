<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Organizations;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\BreadCrumbService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Class UpdateController
 *
 * Handles displaying the form to update an existing organization.
 * @package App\Http\Controllers\Admin\Organizations
 */
class UpdateController extends Controller
{
    /**
     * UpdateController constructor.
     *
     * @param BreadCrumbService $crumbs The service for managing breadcrumbs.
     */
    public function __construct(private readonly BreadCrumbService $crumbs)
    {
    }

    /**
     * Handle the request to display the organization update page.
     *
     * Authorizes the user, sets up breadcrumbs, and returns the view
     * containing the form to update an existing organization.
     *
     * @param Request $request The incoming HTTP request object.
     * @param Organization $organization The organization to be updated.
     * @return View The rendered organization update view.
     */
    public function __invoke(Request $request, Organization $organization): View
    {
        $this->authorize('update', $organization);

        $this->crumbs->addRoute('Command Staff', 'admin.display');
        $this->crumbs->addRoute('Organizations', 'admin.organizations.list');
        $this->crumbs->add('Update');

        $data = [
            'organization' => $organization
        ];

        return view('pages.admin.organizations.update', $data);
    }
}
