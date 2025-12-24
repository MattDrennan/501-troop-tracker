<?php

namespace Database\Factories;

use App\Models\EventUpload;
use App\Models\EventUploadTag;
use App\Models\Trooper;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventUploadTag>
 */
class EventUploadTagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            EventUploadTag::EVENT_UPLOAD_ID => EventUpload::factory(),
            EventUploadTag::TROOPER_ID => Trooper::factory(),
        ];
    }
}
