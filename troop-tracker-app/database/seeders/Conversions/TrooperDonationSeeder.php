<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions;

use App\Models\TrooperDonation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrooperDonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy_donations = DB::table('donations')
            ->join('tt_troopers', 'donations.trooperid', '=', 'tt_troopers.id')
            ->get();

        foreach ($legacy_donations as $donation)
        {
            $d = TrooperDonation::firstWhere('txn_id', $donation->txn_id) ?? new TrooperDonation([TrooperDonation::TXN_ID => $donation->txn_id]);

            $d->trooper_id = $donation->trooperid;
            $d->amount = $donation->amount;
            $d->txn_type = $donation->txn_type;
            $d->created_at = $donation->datetime;
            $d->updated_at = $donation->datetime;

            $d->save();
        }
    }
}