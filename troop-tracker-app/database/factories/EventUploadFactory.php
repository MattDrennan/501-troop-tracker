<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventUpload;
use App\Models\Trooper;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventUpload>
 */
class EventUploadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            EventUpload::EVENT_ID => Event::factory(),
            EventUpload::TROOPER_ID => Trooper::factory(),
            EventUpload::FILENAME => Str::random(12) . '.jpg',
        ];
    }
}
