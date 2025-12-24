<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Trooper;
use App\Models\TrooperAssignment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Handles displaying the notification settings form via an HTMX request.
 */
class NotificationsListHtmxController extends Controller
{
    /**
     * Handle the incoming request to display the notification settings.
     *
     * @param Request $request The incoming HTTP request.
     * @return View The rendered notification settings view.
     */
    public function __invoke(Request $request): View
    {
        $data = $this->getTrooperNotifications();

        return view('pages.account.notifications', $data);
    }

    private function getTrooperNotifications(): array
    {
        $trooper = Trooper::findOrFail(Auth::user()->id);

        $organizations = Organization::fullyLoaded()->get();

        $trooper_assignments = $trooper->trooper_assignments()
            ->where(TrooperAssignment::NOTIFY, true)
            ->pluck(TrooperAssignment::ORGANIZATION_ID)
            ->toArray();

        foreach ($organizations as $organization)
        {
            $organization->selected = in_array($organization->id, $trooper_assignments);

            foreach ($organization->organizations as $region)
            {
                $region->selected = in_array($region->id, $trooper_assignments);

                foreach ($region->organizations as $unit)
                {
                    $unit->selected = in_array($unit->id, $trooper_assignments);
                }
            }
        }

        $data = [
            'organizations' => $organizations,
            'instant_notification' => $trooper->instant_notification,
            'attendance_notification' => $trooper->attendance_notification,
            'command_staff_notification' => $trooper->command_staff_notification,
        ];

        return $data;
    }
}
