<?php

namespace Database\Factories;

use App\Models\Award;
use App\Models\Trooper;
use App\Models\TrooperAward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrooperAward>
 */
class TrooperAwardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            TrooperAward::TROOPER_ID => Trooper::factory(),
            TrooperAward::AWARD_ID => Award::factory(),
        ];
    }
}
