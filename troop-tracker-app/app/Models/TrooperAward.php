<?php

namespace App\Models;

use App\Models\Base\TrooperAward as BaseTrooperAward;
use App\Models\Concerns\HasTrooperStamps;
use App\Models\Scopes\HasTrooperAwardScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrooperAward extends BaseTrooperAward
{
    use HasTrooperAwardScopes;
    use HasFactory;
    use HasTrooperStamps;
}
