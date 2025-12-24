<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = DB::table('settings')->first();

        $support_goal = Setting::find('support_goal') ?? new Setting(['key' => 'support_goal']);

        $support_goal->value = $settings->supportgoal;

        $support_goal->save();
    }
}