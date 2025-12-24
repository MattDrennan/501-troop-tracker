<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions;

use App\Models\TrooperCostume;
use Database\Seeders\Conversions\Traits\HasEnumMaps;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrooperCostumeSeeder extends Seeder
{
    use HasEnumMaps;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $favorites = DB::table('favorite_costumes')
            ->join('tt_troopers', 'favorite_costumes.trooperid', '=', 'tt_troopers.id')
            ->join('tt_costumes', 'favorite_costumes.costumeid', '=', 'tt_costumes.id')
            ->select('favorite_costumes.*')
            ->get();

        foreach ($favorites as $favorite)
        {
            $t = TrooperCostume::where(TrooperCostume::TROOPER_ID, $favorite->trooperid)
                ->where(TrooperCostume::COSTUME_ID, $favorite->costumeid)
                ->first();

            if ($t == null)
            {
                $t = new TrooperCostume();

                $t->trooper_id = $favorite->trooperid;
                $t->costume_id = $favorite->costumeid;

                $t->save();
            }
        }
    }
}