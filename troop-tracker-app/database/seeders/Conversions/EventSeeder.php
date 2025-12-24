<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions;

use App\Models\Event;
use Database\Seeders\Conversions\Traits\HasSquadMaps;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    use HasSquadMaps;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $squad_maps = $this->getSquadMap();

        $legacy_events = DB::table('events')->get();

        foreach ($legacy_events as $event)
        {
            $e = Event::find($event->id) ?? new Event(['id' => $event->id]);

            $e->name = $event->name;
            $e->starts_at = $event->dateStart;
            $e->ends_at = $event->dateEnd;

            $e->limit_participants = $event->limitedEvent ?? false;
            $e->total_troopers_allowed = $event->limitedEvent ? $event->limitTotalTroopers : null;
            $e->total_handlers_allowed = $event->limitedEvent ? $event->limitHandlers : null;
            $e->closed = $event->closed;

            $e->charity_direct_funds = $event->charityDirectFunds;
            $e->charity_indirect_funds = $event->charityIndirectFunds;
            $e->charity_name = $event->charityName;
            $e->charity_hours = $event->charityAddHours;

            $e->save();
        }
    }
}