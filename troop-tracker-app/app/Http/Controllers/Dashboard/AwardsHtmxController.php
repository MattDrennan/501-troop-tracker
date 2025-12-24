<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TrooperAward;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handles HTMX requests to display a trooper's awards on the dashboard.
 *
 * This controller fetches all awards earned by a given trooper and returns
 * a view partial containing the list.
 */
class AwardsHtmxController extends Controller
{
    /**
     * Handle the incoming request to display the awards partial.
     *
     * @param Request $request The incoming HTTP request, which may contain a 'trooper_id'.
     * @return View The rendered view partial for the trooper's awards.
     */
    public function __invoke(Request $request): View
    {
        $trooper_id = (int) $request->get('trooper_id', Auth::user()->id);

        $data = [
            'awards' => TrooperAward::byTrooper($trooper_id)->get(),
        ];

        return view('pages.dashboard.awards', $data);
    }
}
