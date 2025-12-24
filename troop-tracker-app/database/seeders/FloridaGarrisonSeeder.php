<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Conversions\AwardSeeder;
use Database\Seeders\Conversions\CostumeSeeder;
use Database\Seeders\Conversions\EventSeeder;
use Database\Seeders\Conversions\EventTrooperSeeder;
use Database\Seeders\Conversions\EventUploadSeeder;
use Database\Seeders\Conversions\SettingSeeder;
use Database\Seeders\Conversions\TrooperAwardSeeder;
use Database\Seeders\Conversions\TrooperCostumeSeeder;
use Database\Seeders\Conversions\TrooperDonationSeeder;
use Database\Seeders\Conversions\TrooperOrganizationSeeder;
use Database\Seeders\Conversions\TrooperSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class FloridaGarrisonSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(OrganizationSeeder::class);
        $this->call(OrganizationSeeder::class); // NO IDEA but needs a second run for the node_path & depth
        $this->call(OrganizationSeeder::class); // maybe a third - can't figure this one out
        $this->call(CostumeSeeder::class);

        $this->call(SettingSeeder::class);
        $this->call(TrooperSeeder::class);
        $this->call(TrooperDonationSeeder::class);
        $this->call(TrooperOrganizationSeeder::class);
        $this->call(TrooperCostumeSeeder::class);

        $this->call(EventSeeder::class);
        $this->call(EventUploadSeeder::class);
        $this->call(EventTrooperSeeder::class);

        $this->call(AwardSeeder::class);
        $this->call(TrooperAwardSeeder::class);

        if (config('app.debug'))
        {
            $this->call(ActorSeeder::class);
        }

        Artisan::call('app:calculate-trooper-achievements');
    }
}
