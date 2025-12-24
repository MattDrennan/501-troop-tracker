<?php

namespace Database\Factories;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\Organization;
use App\Models\Trooper;
use App\Models\TrooperOrganization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrooperOrganization>
 */
class TrooperOrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            TrooperOrganization::TROOPER_ID => Trooper::factory(),
            TrooperOrganization::IDENTIFIER => 'TK' . uniqid(),
            TrooperOrganization::ORGANIZATION_ID => Organization::factory(),
        ];
    }
}
