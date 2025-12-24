<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\OrganizationType;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $this->loadOrganizations();
        $this->loadRegions();
        $this->loadUnits();

        Organization::resequenceAll();
    }

    private function loadOrganizations()
    {
        $organizations = [
            [
                'name' => '501st Legion',
                'description' => 'Imperial costuming organization focused on Star Wars villains.',
                'identifier_display' => 'TKID',
                'identifier_validation' => 'integer|between:1000,99999',
            ],
            [
                'name' => 'Rebel Legion',
                'description' => 'Rebel-aligned Star Wars costuming group.',
                'identifier_display' => 'Forum Username',
            ],
            [
                'name' => 'Mandalorian Mercs',
                'description' => 'Custom Mandalorian armor builders and costumers.',
                'identifier_display' => 'CAT #',
                'identifier_validation' => 'integer',
            ],
            [
                'name' => 'Dark Empire',
                'description' => 'Expanded universe costuming group for dark side characters.',
                'identifier_display' => '#',
                'identifier_validation' => 'integer',
            ],
            [
                'name' => 'Droid Builders',
                'description' => 'Star Wars droid construction and robotics enthusiast group.',
                'identifier_display' => '#',
                'identifier_validation' => 'integer',
            ],
            [
                'name' => 'Saber Guild',
                'description' => 'Lightsaber performance and Jedi/Sith costuming group.',
                'identifier_display' => 'SG #',
                'identifier_validation' => 'integer',
            ],
        ];

        foreach ($organizations as $data)
        {
            $org = Organization::where('name', $data['name'])->first() ?? new Organization();

            $org->name = $data['name'];
            $org->description = $data['description'];
            $org->identifier_display = $data['identifier_display'] ?? '';
            $org->identifier_validation = $data['identifier_validation'] ?? '';
            $org->type = OrganizationType::Organization;

            $org->save();
        }
    }

    private function loadRegions()
    {
        $regions = [
            ['parent' => '501st Legion', 'name' => 'Florida Garrison'],
            ['parent' => 'Rebel Legion', 'name' => 'Ra Kura Base'],
            ['parent' => 'Mandalorian Mercs', 'name' => 'House Buurenaar Verda'],
            ['parent' => 'Dark Empire', 'name' => 'Dark Empire Florida'],
            ['parent' => 'Saber Guild', 'name' => 'Saber Guild - Talon Temple'],
            ['parent' => 'Droid Builders', 'name' => 'Florida Droid Builders'],
        ];

        foreach ($regions as $data)
        {
            $parent = Organization::where('name', $data['parent'])->first();

            if ($parent)
            {
                $region = Organization::where('name', $data['name'])
                    ->where('parent_id', $parent->id)
                    ->first() ?? new Organization();

                $region->name = $data['name'];
                $region->parent_id = $parent->id;
                $region->type = OrganizationType::Region;

                $region->save();
            }
        }
    }

    private function loadUnits()
    {
        $units = [
            ['region' => 'Florida Garrison', 'name' => 'Everglades Squad'],
            ['region' => 'Florida Garrison', 'name' => 'Makaze Squad'],
            ['region' => 'Florida Garrison', 'name' => 'Tampa Bay Squad'],
            ['region' => 'Florida Garrison', 'name' => 'Squad 7'],
            ['region' => 'Florida Garrison', 'name' => 'Parjai Squad'],
            ['region' => 'House Buurenaar Verda', 'name' => 'Aiwha Riders Clan'],
            ['region' => 'House Buurenaar Verda', 'name' => 'Batuu Clan'],
            ['region' => 'House Buurenaar Verda', 'name' => 'Drexl Clan'],
            ['region' => 'House Buurenaar Verda', 'name' => 'Scarif Clan'],
            ['region' => 'Dark Empire Florida', 'name' => 'Shadow Cell'],
            ['region' => 'Saber Guild - Talon Temple', 'name' => 'Performance Team'],
            ['region' => 'Florida Droid Builders', 'name' => 'R2 Builders Tampa'],
            ['region' => 'Florida Droid Builders', 'name' => 'R2 Builders Orlando'],
        ];

        foreach ($units as $data)
        {
            $region = Organization::where('name', $data['region'])->first();

            if ($region)
            {
                $unit = Organization::where('name', $data['name'])
                    ->where('parent_id', $region->id)
                    ->first() ?? new Organization();

                $unit->name = $data['name'];
                $unit->parent_id = $region->id;
                $unit->type = OrganizationType::Unit;

                $unit->save();
            }
        }
    }
}