<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $starts_at = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $ends_at = (clone $starts_at)->modify('+4 hours');

        return [
            Event::NAME => $this->faker->sentence(4),
            Event::STARTS_AT => $starts_at,
            Event::ENDS_AT => $ends_at,
            Event::LIMIT_PARTICIPANTS => $this->faker->boolean(),
            Event::TOTAL_TROOPERS_ALLOWED => $this->faker->numberBetween(10, 50),
            Event::TOTAL_HANDLERS_ALLOWED => $this->faker->numberBetween(2, 5),
            Event::CLOSED => false,
        ];
    }

    /**
     * Indicate that the event is closed (historical).
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function closed(): Factory
    {
        return $this->state(function (array $attributes)
        {
            return [
                Event::CLOSED => true,
                Event::STARTS_AT => $this->faker->dateTimeBetween('-2 years', '-1 month'),
                Event::ENDS_AT => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            ];
        });
    }
}