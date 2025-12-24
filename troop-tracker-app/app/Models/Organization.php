<?php

namespace App\Models;

use App\Enums\OrganizationType;
use App\Models\Base\Organization as BaseOrganization;
use App\Models\Concerns\HasObserver;
use App\Models\Concerns\HasTrooperStamps;
use App\Models\Scopes\HasOrganizationScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Organization extends BaseOrganization
{
    use HasObserver;
    use HasOrganizationScopes;
    use HasFactory;
    use HasTrooperStamps;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts()
    {
        return array_merge($this->casts, [
            self::TYPE => OrganizationType::class,
        ]);
    }

    /**
     * Alias for organization()
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Organization, Organization>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, self::PARENT_ID);
    }

    public function event_troopers(): HasManyThrough
    {
        return $this->hasManyThrough(EventTrooper::class, Costume::class);
    }

    public static function resequenceAll()
    {
        $organizations = self::ofTypeOrganizations()->orderBy('name')->get();

        $seq = 900;

        foreach ($organizations as $organization)
        {
            $seq += 100;

            $organization->updateQuietly([self::SEQUENCE => $seq]);

            $regions = $organization->organizations()->ofTypeRegions()->orderBy('name')->get();

            foreach ($regions as $region)
            {
                $seq += 100;

                $region->updateQuietly([self::SEQUENCE => $seq]);

                $units = $region->organizations()->ofTypeUnits()->orderBy('name')->get();

                foreach ($units as $unit)
                {
                    $seq += 100;

                    $unit->updateQuietly([self::SEQUENCE => $seq]);
                }
            }
        }
    }
}
