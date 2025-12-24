<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TrooperDonation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handles HTMX requests to display a trooper's donation history on the dashboard.
 *
 * This controller fetches all donations made by a given trooper and returns
 * a view partial containing the list.
 */
class DonationsHtmxController extends Controller
{
    /**
     * Handle the incoming request to display the donations partial.
     *
     * @param Request $request The incoming HTTP request, which may contain a 'trooper_id'.
     * @return View The rendered view partial for the trooper's donations.
     */
    public function __invoke(Request $request): View
    {
        $trooper_id = (int) $request->get('trooper_id', Auth::user()->id);

        $data = [
            'donations' => TrooperDonation::byTrooper($trooper_id)->get(),
        ];

        return view('pages.dashboard.donations', $data);
    }
}
