<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions;

use App\Models\Costume;
use Database\Seeders\Conversions\Traits\HasClubMaps;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CostumeSeeder extends Seeder
{
    use HasClubMaps;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $club_map = collect($this->getClubMap());

        $legacy_costumes = DB::table('costumes')->get();

        foreach ($legacy_costumes as $column => $costume)
        {
            $club = $club_map->firstWhere('legacy_id', $costume->club) ?? null;

            if (is_null($club))
            {
                continue;
            }

            $c = Costume::where(Costume::NAME, $costume->costume)
                ->where(Costume::ORGANIZATION_ID, $club['id'])
                ->first();

            if ($c == null)
            {
                $c = new Costume(['id' => $costume->id]);
            }

            $c->name = $costume->costume;
            $c->organization_id = $club['id'];

            $c->save();
        }
    }
}