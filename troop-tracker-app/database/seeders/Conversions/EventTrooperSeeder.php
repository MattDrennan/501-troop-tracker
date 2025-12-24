<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions;

use App\Enums\TrooperEventStatus;
use App\Models\Costume;
use App\Models\EventTrooper;
use Database\Seeders\Conversions\Traits\HasSquadMaps;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTrooperSeeder extends Seeder
{
    use HasSquadMaps;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $costumes = Costume::all()->pluck(Costume::ID)->toArray();

        $status_map = array_values(array_filter(
            TrooperEventStatus::cases(),
            fn($case) => $case !== TrooperEventStatus::None
        ));

        $sign_ups = DB::table('event_sign_up')
            ->join('tt_troopers', 'event_sign_up.trooperid', '=', 'tt_troopers.id')
            ->join('tt_events', 'event_sign_up.troopid', '=', 'tt_events.id')
            ->select('event_sign_up.*')
            ->get();

        foreach ($sign_ups as $sign_up)
        {
            $e = EventTrooper::where(EventTrooper::EVENT_ID, $sign_up->troopid)
                ->where(EventTrooper::TROOPER_ID, $sign_up->trooperid)
                ->first();

            if ($e == null)
            {
                $e = new EventTrooper();
            }

            $e->event_id = $sign_up->troopid;
            $e->trooper_id = $sign_up->trooperid;
            $e->costume_id = in_array($sign_up->costume, $costumes) ? $sign_up->costume : null;
            $e->backup_costume_id = in_array($sign_up->costume_backup, $costumes) ? $sign_up->costume_backup : null;
            $e->status = $status_map[$sign_up->status];

            $e->save();
        }
    }
}