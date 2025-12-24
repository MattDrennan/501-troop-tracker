<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\EventUpload;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handles HTMX requests to display uploads a trooper is tagged in.
 *
 * This controller fetches all event uploads where a given trooper has been tagged
 * and returns a view partial containing the list of those uploads.
 */
class TaggedUploadsHtmxController extends Controller
{
    /**
     * Handle the incoming request to display the tagged uploads partial.
     *
     * @param Request $request The incoming HTTP request, which may contain a 'trooper_id'.
     * @return View The rendered view partial for the trooper's tagged uploads.
     */
    public function __invoke(Request $request): View
    {
        $trooper_id = (int) $request->get('trooper_id', Auth::user()->id);

        $data = [
            'uploads' => EventUpload::byTrooper($trooper_id)->get(),
        ];

        return view('pages.dashboard.tagged-uploads', $data);
    }
}
