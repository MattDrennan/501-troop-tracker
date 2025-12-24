<?php

namespace App\Models;

use App\Models\Base\EventCostume as BaseEventCostume;
use App\Models\Concerns\HasTrooperStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventCostume extends BaseEventCostume
{
    use HasFactory;
    use HasTrooperStamps;
}
