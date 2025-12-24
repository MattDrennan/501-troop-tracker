<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Organizations;

use App\Enums\OrganizationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Organizations\CreateRequest;
use App\Models\Organization;
use App\Services\FlashMessageService;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;

/**
 * Class CreateSubmitController
 *
 * Handles the submission of the form for creating a new organization.
 * @package App\Http\Controllers\Admin\Organizations
 */
class CreateSubmitController extends Controller
{
    /**
     * CreateSubmitController constructor.
     *
     * @param FlashMessageService $flash The service for displaying flash messages.
     */
    public function __construct(private readonly FlashMessageService $flash)
    {
    }

    /**
     * Handle the incoming request to create a new organization.
     *
     * Validates the request, creates a new organization under the given parent,
     * determines its type, saves it, and then redirects with a success message.
     *
     * @param CreateRequest $request The validated request containing the new organization's data.
     * @param Organization $parent The parent organization.
     * @return RedirectResponse A redirect response to the organization list.
     */
    public function __invoke(CreateRequest $request, Organization $parent): RedirectResponse
    {
        $organization = new Organization();

        $organization->parent_id = $parent->id;
        $organization->name = $request->validated('name');

        if ($parent->type == OrganizationType::Organization)
        {
            $organization->type = OrganizationType::Region;
        }
        elseif ($parent->type == OrganizationType::Region)
        {
            $organization->type = OrganizationType::Unit;
        }
        else
        {
            throw new InvalidArgumentException('Cannot create a sub-organization under the specified parent type.');
        }

        $organization->save();

        Organization::resequenceAll();

        $this->flash->created($organization);

        return redirect()->route('admin.organizations.list');
    }
}
