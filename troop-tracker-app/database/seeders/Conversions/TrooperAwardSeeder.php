<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions;

use App\Models\TrooperAward;
use Database\Seeders\Conversions\Traits\HasEnumMaps;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrooperAwardSeeder extends Seeder
{
    use HasEnumMaps;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy_trooper_awards = DB::table('award_troopers')
            ->join('tt_troopers', 'award_troopers.trooperid', '=', 'tt_troopers.id')
            ->select('award_troopers.*')
            ->get();

        foreach ($legacy_trooper_awards as $award)
        {
            $a = TrooperAward::find($award->id) ?? new TrooperAward(['id' => $award->id]);

            $a->award_id = $award->awardid;
            $a->trooper_id = $award->trooperid;

            $a->created_at = $award->awarded;
            $a->updated_at = $award->awarded;

            $a->save();
        }
    }
}