<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions;

use App\Models\Award;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy_awards = DB::table('awards')->get();

        foreach ($legacy_awards as $award)
        {
            $a = Award::find($award->id) ?? new Award(['id' => $award->id]);

            $a->name = $award->title;

            $a->save();
        }
    }
}