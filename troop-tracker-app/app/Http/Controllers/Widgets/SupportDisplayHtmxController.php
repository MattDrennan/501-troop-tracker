<?php

declare(strict_types=1);

namespace App\Http\Controllers\Widgets;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\TrooperDonation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Handles displaying the support widget via an HTMX request.
 */
class SupportDisplayHtmxController extends Controller
{

    /**
     * Handle the incoming request to display the support widget.
     *
     * @param Request $request The incoming HTTP request.
     * @return View The rendered support widget view.
     */
    public function __invoke(Request $request): View
    {
        $donations = TrooperDonation::forMonth()->sum(TrooperDonation::AMOUNT);

        $support_goal = setting('support_goal', 0);

        $progress = 0;

        if ($support_goal > 0)
        {
            $progress = $donations / $support_goal;
        }

        $data = [
            'goal' => $support_goal,
            'progress' => number_format($progress * 100, 0),
            'message' => $this->getMessage()
        ];

        return view('pages.widgets.support', $data);
    }

    /**
     * Retrieves a message based on the current day of the week.
     *
     * @return string The message for the current day.
     */
    private function getMessage(): string
    {
        $name = config('tracker.forum.name');

        $messages = [
            0 => "The $name is rallying support to maintain operations, reinforce mission " .
                "logistics, and keep our digital gear inspection ready - including the tactical systems behind our website, event " .
                "coordination, and member tools. Every credit counts toward keeping our settings battle-ready, our systems secure, " .
                "and our deployments fully operational. ",
            1 => "The $name is mobilizing resources to sustain operations, reinforce mission " .
                "logistics, and hold the line with our digital infrastructure. Your support helps keep our systems sharp, our " .
                "deployments smooth, and our settings mission-ready.",
            2 => "Settings don't march alone. Every credit fuels the $name's operations, " .
                "mission logistics, and the digital command center that powers our website and events. Stand with the Legion - help " .
                "us stay battle-ready.",
            3 => "From clone barracks to command consoles, the $name runs on community support. " .
                "Donations help us maintain operations, coordinate missions, and the tech behind our website - the real " .
                "gear behind the gear.",
            4 => "Operational stability, mission logistics, and tactical system upgrades are underway. The " .
                "$name requests your support to maintain full deployment capability. Your " .
                "contribution ensures our digital arsenal stays sharp.",
            5 => "<b>Incoming transmission</b>: The $name is requesting support to maintain " .
                "operations, reinforce mission logistics, and upgrade our tactical systems - including the website that keeps our " .
                "ranks organized. Your credits keep the Legion strong.",
        ];

        $index = now()->dayOfWeek;

        return $messages[$index] ?? $messages[0];
    }
}
