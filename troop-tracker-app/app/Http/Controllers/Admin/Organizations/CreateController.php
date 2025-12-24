<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Organizations;

use App\Enums\OrganizationType;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\BreadCrumbService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Class CreateController
 *
 * Handles displaying the form to create a new organization under a parent.
 * @package App\Http\Controllers\Admin\Organizations
 */
class CreateController extends Controller
{
    /**
     * CreateController constructor.
     *
     * @param BreadCrumbService $crumbs The service for managing breadcrumbs.
     */
    public function __construct(private readonly BreadCrumbService $crumbs)
    {
    }

    /**
     * Handle the request to display the organization creation page.
     *
     * Authorizes the user, sets up breadcrumbs, and returns the view
     * containing the form to create a new sub-organization.
     *
     * @param Request $request The incoming HTTP request object.
     * @param Organization $parent The parent organization under which to create a new one.
     * @return View The rendered organization creation view.
     */
    public function __invoke(Request $request, Organization $parent): View
    {
        $this->authorize('update', $parent);

        $this->crumbs->addRoute('Command Staff', 'admin.display');
        $this->crumbs->addRoute('Organizations', 'admin.organizations.list');
        $this->crumbs->add('Create');

        $organization = new Organization();

        if ($parent->type == OrganizationType::Organization)
        {
            $organization->type = OrganizationType::Region;
        }
        elseif ($parent->type == OrganizationType::Region)
        {
            $organization->type = OrganizationType::Unit;
        }

        $data = [
            'parent' => $parent,
            'organization' => $organization
        ];

        return view('pages.admin.organizations.create', $data);
    }
}
