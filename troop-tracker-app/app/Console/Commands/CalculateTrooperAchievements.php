<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\EventTrooper;
use App\Models\TrooperAchievement;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Artisan command to calculate and store trooper achievements based on their event history.
 *
 * This command aggregates event data for each trooper, such as total troops,
 * volunteer hours, and funds raised, and then updates their corresponding
 * achievements in the database.
 */
class CalculateTrooperAchievements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-trooper-achievements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate trooper achievements.';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->storeAchievements();
    }

    /**
     * Fetches trooper event statistics and stores their achievements.
     *
     * This method iterates through each trooper's aggregated event data,
     * determines which achievements they have earned, and updates the
     * `trooper_achievements` table accordingly.
     *
     * @return void
     */
    private function storeAchievements(): void
    {
        $trooper_events = $this->getTrooperEvents();

        for ($i = 0, $len = $trooper_events->count(); $i < $len; $i++)
        {
            $trooper_event = $trooper_events[$i];
            $count = $trooper_event->event_count;
            $hours = $trooper_event->total_hours;
            $direct_funds = $trooper_event->total_direct;
            $indirect_funds = $trooper_event->total_indirect;

            $where = [TrooperAchievement::TROOPER_ID => $trooper_events[$i]->trooper_id];

            $values = [
                TrooperAchievement::TROOPER_RANK => ($i + 1),
                TrooperAchievement::FIRST_TROOP_COMPLETED => $count >= 1,
                TrooperAchievement::TROOPED_10 => $count >= 10,
                TrooperAchievement::TROOPED_25 => $count >= 25,
                TrooperAchievement::TROOPED_50 => $count >= 50,
                TrooperAchievement::TROOPED_75 => $count >= 75,
                TrooperAchievement::TROOPED_100 => $count >= 100,
                TrooperAchievement::TROOPED_150 => $count >= 150,
                TrooperAchievement::TROOPED_200 => $count >= 200,
                TrooperAchievement::TROOPED_250 => $count >= 250,
                TrooperAchievement::TROOPED_300 => $count >= 300,
                TrooperAchievement::TROOPED_400 => $count >= 400,
                TrooperAchievement::TROOPED_500 => $count >= 500,
                TrooperAchievement::TROOPED_501 => $count >= 501,
                TrooperAchievement::VOLUNTEER_HOURS => $hours,
                TrooperAchievement::DIRECT_FUNDS => $direct_funds,
                TrooperAchievement::INDIRECT_FUNDS => $indirect_funds,
            ];

            TrooperAchievement::updateOrCreate($where, $values);
        }
    }

    /**
     * Retrieves aggregated event data for all troopers.
     *
     * @return Collection
     */
    private function getTrooperEvents(): Collection
    {
        $select = 'tt_event_troopers.trooper_id, ' .
            'COUNT(1) as event_count, ' .
            'SUM(tt_events.charity_direct_funds) as total_direct, ' .
            'SUM(tt_events.charity_indirect_funds) as total_indirect, ' .
            'SUM(TIMESTAMPDIFF(HOUR, tt_events.starts_at, tt_events.ends_at) + tt_events.charity_hours) as total_hours';

        $trooper_events = EventTrooper::selectRaw($select)
            ->join('tt_events', 'tt_event_troopers.event_id', '=', 'tt_events.id')
            ->where('tt_events.closed', true)
            ->groupBy('tt_event_troopers.trooper_id')
            ->orderByDesc('event_count')
            ->get();

        return $trooper_events;
    }
}
