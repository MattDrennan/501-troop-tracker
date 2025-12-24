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
 * Handles the submission for adding a new trooper costume via an HTMX request.
 */
class CostumesSubmitHtmxController extends Controller
{
    /**
     * Handle the incoming request to add a trooper costume.
     *
     * This method validates that the user is a member of the organization associated with the costume
     * before adding it to their troopers list. It then returns a view partial containing
     * the updated list of trooper costumes.
     *
     * @param Request $request The incoming HTTP request.
     * @return View The rendered trooper costumes view, intended for an HTMX response.
     */
    public function __invoke(Request $request): View
    {
        $trooper = Trooper::findOrFail(Auth::user()->id);

        $organization_id = (int) $request->input('organization_id', -1);
        $costume_id = (int) $request->input('costume_id', -1);

        if ($organization_id > -1 && $costume_id > -1)
        {
            $organization = Organization::withActiveTroopers($trooper->id)
                ->where(Organization::ID, $organization_id)
                ->first();

            if (isset($organization))
            {
                $costume = $organization->costumes()
                    ->where(Costume::ID, $costume_id)
                    ->first();

                if (isset($costume))
                {
                    $trooper->attachCostume($costume->id);
                }
            }
        }

        $data = [
            'organizations' => collect(),
            'selected_organization' => null,
            'costumes' => collect(),
            'trooper_costumes' => $trooper->costumes,
        ];

        return view('pages.account.costumes', $data);
    }
}
