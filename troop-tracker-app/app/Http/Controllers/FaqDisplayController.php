<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Displays the Frequently Asked Questions (FAQ) page.
 *
 * This controller is responsible for gathering data for the FAQ page,
 * which primarily consists of a list of tutorial videos.
 */
class FaqDisplayController extends Controller
{
    /**
     * Handle the incoming request to display the FAQ page.
     *
     * @param Request $request The incoming HTTP request.
     * @return View The rendered FAQ page view.
     */
    public function __invoke(Request $request): View
    {
        $data = [
            'videos' => [
                'vFP6posJt70' => 'Troop Tracker - Overview Video (After Account Setup)',
                'j0Z5SB6TVOg' => 'Troop Tracker - How To Video',
                'ycwFdQQvGoc' => 'Troop Tracker - For Command Staff',
                '_UhtyHbL8uY' => 'How to add an iOS shortcut',
                'S4Xu_N4ByBs' => 'How to add an Android shortcut',
                'aODHyWMMVUQ' => 'How to upload photos',
                'W-wcceu6xzI' => 'How to view milestones and awards',
                'C0WCxIRZafQ' => 'How to add a friend to a troop',
                'mDeJaANqLIk' => 'How to add someone to a troop without them being a member or having tracker access',
                '-pXqGZLiVpM' => 'How to search for troops and troop counts',
                '17UPK4AoKxg' => 'How to view past troops',
                'H-nnM5jndZA' => 'How to sort by unit',
                'Rn3EnhudHyc' => 'How to show troops your signed up for',
                '02ERoFw7XlY' => 'How to use calendar view',
                'y_I8ssRjek8' => 'How to search troops on the homepage',
                '5pp7_FKg7cI' => 'How to subscribe to events',
                'cefnojYUy-Y' => 'How to add an event to your calendar',
                'tS-bCXbCzs4' => 'How to add discussion to a troop',
                'IPykBoeDGcg' => 'How to change the theme',
                'YLjiVGgqe-Y' => 'How to search costumes',
                'MFdLu9aWwlI' => 'Command Staff - Create an Event',
                'WADjtDcmeJo' => 'Command Staff - Edit an Event',
                'c8IT_s0qSxc' => 'Command Staff - Edit Roster',
            ]
        ];

        return view('pages.faq', $data);
    }
}
