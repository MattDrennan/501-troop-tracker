<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\Organization;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Handles HTMX requests for updating organization selections on the registration page.
 */
class RegisterHtmxController
{
    /**
     */
    public function __construct()
    {
    }

    /**
     * Handle the incoming HTMX request to render a organization selection partial.
     *
     * @param Request $request The incoming HTTP request.
     * @param Organization $organization The organization model instance from route model binding.
     * @return View The rendered organization selection partial view.
     */
    public function __invoke(Request $request, Organization $organization): View
    {
        $organization->selected = $request->input("organizations.{$organization->id}.selected") === '1';

        $data = [
            'organization' => $organization,
        ];

        if ($organization->organizations->count() == 1)
        {
            $region = $organization->organizations->first();

            $region->selected = true;
        }
        else
        {
            $region_id = (int) $request->input("organizations.{$organization->id}.region_id");

            if (isset($region_id))
            {
                foreach ($organization->organizations as $region)
                {
                    if ($region->id == $region_id)
                    {
                        $region->selected = true;

                        break;
                    }
                }
            }
        }

        return view('pages.auth.organization-selection', $data);
    }
}
