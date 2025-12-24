<?php

namespace Database\Factories;

use App\Enums\MembershipStatus;
use App\Models\Trooper;
use App\Models\TrooperAchievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrooperAchievement>
 */
class TrooperAchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            TrooperAchievement::TROOPER_ID => Trooper::factory(),
        ];
    }
}
