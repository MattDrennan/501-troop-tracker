<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use App\Models\Base\TrooperOrganization as BaseTrooperOrganization;
use App\Models\Scopes\HasTrooperOrganizationScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrooperOrganization extends BaseTrooperOrganization
{
    use HasFactory;
    use HasTrooperOrganizationScopes;

    protected function casts(): array
    {
        return array_merge($this->casts, [
            self::MEMBERSHIP_STATUS => MembershipStatus::class,
        ]);
    }
}
