<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pickers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handles requests for a view that allows picking an organization.
 *
 * This controller is designed to be used for UI components where a user
 * needs to select an organization from a list, potentially filtered by their
 * moderation permissions.
 */
class OrganizationPickerController extends Controller
{
    /**
     * Handle the incoming request to display the organization picker view.
     *
     * If the 'moderatored_only' query parameter is present, it fetches only the
     * organizations the authenticated user is assigned to moderate. Otherwise,
     * it returns an empty list.
     *
     * @param Request $request The incoming HTTP request.
     * @return View The rendered organization picker view.
     */
    public function __invoke(Request $request): View
    {
        $trooper = Auth::user();

        $property = $request->query('property');

        if ($property == null)
        {
            throw new Exception("Missing property parameter");
        }

        $organizations = collect([]);

        if ($request->query('moderated_only'))
        {
            if ($trooper->isAdministrator())
            {
                $organizations = Organization::orderBy(Organization::SEQUENCE)
                    ->get();
            }
            else
            {
                $organizations = Organization::moderatedBy($trooper)
                    ->orderBy(Organization::SEQUENCE)
                    ->get();
            }
        }

        $data = [
            'organizations' => $organizations,
            'property' => $property
        ];

        return view('pickers.organization', $data);
    }
}
