<?php

namespace Database\Factories;

use App\Enums\MembershipStatus;
use App\Models\Trooper;
use App\Models\TrooperDonation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrooperDonation>
 */
class TrooperDonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            TrooperDonation::TROOPER_ID => Trooper::factory(),
            TrooperDonation::TXN_ID => uniqid(),
            TrooperDonation::AMOUNT => random_int(1, 100)
        ];
    }
}
