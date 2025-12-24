<?php

namespace Database\Factories;

use App\Models\Costume;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Costume>CostumeFactory
 */
class CostumeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Costume::NAME => fake()->name(),
            Costume::ORGANIZATION_ID => Organization::factory()
        ];
    }
}
