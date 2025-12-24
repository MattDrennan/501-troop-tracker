<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Costume;
use App\Models\Organization;
use App\Models\Trooper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handles displaying the trooper costumes management interface via an HTMX request.
 */
class CostumesListHtmxController extends Controller
{
    /**
     * Handle the incoming request to display the trooper costumes interface.
     *
     * This method fetches the user's assigned organizations and their trooper costumes.
     * If a 'organization_id' is provided, it also fetches the available costumes for that specific organization.
     *
     * @param Request $request The incoming HTTP request.
     * @return View The rendered trooper costumes view.
     */
    public function __invoke(Request $request): View
    {
        $trooper = Trooper::findOrFail(Auth::user()->id);

        $organizations = Organization::withActiveTroopers($trooper->id)->get();

        $selected_organization = null;
        $costumes = [];

        if ($request->has('organization_id'))
        {
            $organization_id = $request->get('organization_id');

            $selected_organization = $organizations->firstWhere(Organization::ID, $organization_id);

            if (isset($selected_organization))
            {
                $assigned_costume_ids = $trooper->costumes
                    ->where('organization_id', $selected_organization->id)
                    ->pluck('id');

                $costumes = $selected_organization->costumes()
                    ->with('organization')
                    ->excluding($assigned_costume_ids)
                    ->orderBy(Costume::NAME)
                    ->pluck(Costume::NAME, Costume::ID)
                    ->toArray();
            }
        }

        $data = [
            'organizations' => $organizations,
            'selected_organization' => $selected_organization,
            'costumes' => $costumes,
            'trooper_costumes' => $trooper->costumes,
        ];

        return view('pages.account.costumes', $data);
    }
}
