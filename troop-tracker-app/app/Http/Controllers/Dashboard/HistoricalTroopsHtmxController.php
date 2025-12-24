<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handles HTMX requests to display a trooper's historical troops on the dashboard.
 *
 * This controller fetches past event sign-ups (historical troops) for a given trooper
 * and returns a view partial containing the list, sorted by most recent first.
 */
class HistoricalTroopsHtmxController extends Controller
{
    /**
     * Handle the incoming request to display the historical troops partial.
     *
     * Fetches historical troops for the specified or authenticated trooper,
     * sorts them by end date in descending order, and returns the corresponding view.
     *
     * @param Request $request The incoming HTTP request, which may contain a 'trooper_id'.
     * @return View The rendered view partial for historical troops.
     */
    public function __invoke(Request $request): View
    {
        $trooper_id = (int) $request->get('trooper_id', Auth::user()->id);

        $troops = Event::byTrooper($trooper_id, true)
            ->orderByDesc(Event::ENDS_AT)
            ->get();

        $data = [
            'historical_troops' => $troops,
        ];

        return view('pages.dashboard.historical-troops', $data);
    }
}
