<?php

namespace Database\Factories;

use App\Enums\OrganizationType;
use App\Models\Costume;
use App\Models\Organization;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Organization::NAME => '501st-' . uniqid(),
            Organization::IDENTIFIER_DISPLAY => 'TKID',
            Organization::TYPE => OrganizationType::Organization,
            Organization::IMAGE_PATH_LG => '',
            Organization::IMAGE_PATH_SM => '',
            Organization::NODE_PATH => ''
        ];
    }

    public function region(): static
    {
        return $this->state(fn(array $attributes) => [
            Organization::PARENT_ID => Organization::factory(),
            Organization::TYPE => OrganizationType::Region,
        ]);
    }

    public function unit(): static
    {
        return $this->state(fn(array $attributes) => [
            Organization::PARENT_ID => Organization::factory()->region(),
            Organization::TYPE => OrganizationType::Unit,
        ]);
    }

    public function withCostume(string $name): static
    {
        return $this->afterCreating(function (Organization $organization) use ($name)
        {
            if ($organization->type != OrganizationType::Organization)
            {
                throw new Exception('Invalid Organization Type for a costume: ' . $organization->type);
            }

            $organization->costumes()->create([
                'name' => $name
            ]);
        });
    }
}
