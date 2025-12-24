<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions\Traits;

use App\Models\Organization;

trait HasSquadMaps
{
    /**
     * Maps legacy squad names to their database ID and metadata.
     *
     * @return array<string, array{id: int, logo: string, eventForum: int, userGroup: int}>
     */
    protected function getSquadMap(): array
    {
        // Legacy squad metadata keyed by name
        $legacy_squads = [
            'Everglades Squad' => [
                'legacy_id' => 1,
                'logo' => 'everglades_emblem.png',
                'eventForum' => 9,
                'userGroup' => 44,
            ],
            'Makaze Squad' => [
                'legacy_id' => 2,
                'logo' => 'makaze_emblem.png',
                'eventForum' => 8,
                'userGroup' => 45,
            ],
            'Parjai Squad' => [
                'legacy_id' => 3,
                'logo' => 'parjai_emblem.png',
                'eventForum' => 186,
                'userGroup' => 250,
            ],
            'Squad 7' => [
                'legacy_id' => 4,
                'logo' => 'squad7_emblem.png',
                'eventForum' => 7,
                'userGroup' => 683,
            ],
            'Tampa Bay Squad' => [
                'legacy_id' => 5,
                'logo' => 'tampabay_emblem.png',
                'eventForum' => 73,
                'userGroup' => 43,
            ],
        ];

        $map = [];

        foreach ($legacy_squads as $name => $meta)
        {
            $squad = Organization::firstWhere(Organization::NAME, $name);

            if ($squad)
            {
                $map[$meta['legacy_id']] = [
                    'id' => $squad->id,
                    'logo' => $meta['logo'],
                    'eventForum' => $meta['eventForum'],
                    'userGroup' => $meta['userGroup'],
                ];
            }
            else
            {
                throw new \RuntimeException("Squad not found for name: {$name}");
            }
        }

        return $map;
    }
}