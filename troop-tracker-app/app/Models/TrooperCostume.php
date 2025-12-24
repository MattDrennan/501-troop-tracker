<?php

namespace App\Models;

use App\Models\Base\TrooperCostume as BaseTrooperCostume;
use App\Models\Concerns\HasTrooperStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrooperCostume extends BaseTrooperCostume
{
    use HasFactory;
    use HasTrooperStamps;
}
