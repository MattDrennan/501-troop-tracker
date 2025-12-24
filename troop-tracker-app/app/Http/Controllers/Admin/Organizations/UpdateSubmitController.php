<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Organizations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Organizations\UpdateRequest;
use App\Models\Organization;
use App\Services\FlashMessageService;
use Illuminate\Http\RedirectResponse;

/**
 * Class UpdateSubmitController
 *
 * Handles the submission of the form for updating an existing organization.
 * @package App\Http\Controllers\Admin\Organizations
 */
class UpdateSubmitController extends Controller
{
    /**
     * UpdateSubmitController constructor.
     *
     * @param FlashMessageService $flash The service for displaying flash messages.
     */
    public function __construct(private readonly FlashMessageService $flash)
    {
    }

    /**
     * Handle the incoming request to update an organization.
     *
     * Validates the request, updates the organization's name, saves it,
     * and then redirects with a success message.
     *
     * @param UpdateRequest $request The validated request containing the updated data.
     * @param Organization $organization The organization to be updated.
     * @return RedirectResponse A redirect response to the organization list.
     */
    public function __invoke(UpdateRequest $request, Organization $organization): RedirectResponse
    {
        $organization->name = $request->validated('name');

        $organization->save();

        $this->flash->updated($organization);

        return redirect()->route('admin.organizations.list');
    }
}
