<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions\Traits;

use App\Models\Organization;
use Exception;

trait HasClubMaps
{
    protected function getClubMap(): array
    {
        // Hardcoded squadID → club name
        $legacy_clubs = [
            0 => ['name' => '501st Legion', 'column' => 'p501', 'identity' => 'tkid'],
            6 => ['name' => 'Rebel Legion', 'column' => 'pRebel', 'identity' => 'rebelforum'],
            7 => ['name' => 'Droid Builders', 'column' => 'pDroid', 'identity' => ''],
            8 => ['name' => 'Mandalorian Mercs', 'column' => 'pMando', 'identity' => 'mandoid'],
            //9 => ['name' => 'Other', 'column' => 'pOther', 'identity' => ''],
            10 => ['name' => 'Saber Guild', 'column' => 'pSG', 'identity' => 'sgid'],
            13 => ['name' => 'Dark Empire', 'column' => 'pDE', 'identity' => 'de_id'],
        ];

        // Build final map: squadID → club_id
        $map = [];

        foreach ($legacy_clubs as $legacy_id => $meta)
        {
            $organization = Organization::firstWhere(Organization::NAME, $meta['name']);

            if ($organization)
            {
                $map[$meta['column']] = [
                    'id' => $organization->id,
                    'legacy_id' => $legacy_id,
                    'identity' => $meta['identity'],
                ];
            }
            else
            {
                throw new Exception("Organization not found for name: {$meta['name']}");
            }
        }

        return $map;
    }
}