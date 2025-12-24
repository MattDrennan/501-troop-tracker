<?php

namespace App\Models;

use App\Models\Base\Award as BaseAward;
use App\Models\Concerns\HasTrooperStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Award extends BaseAward
{
    use HasFactory;
    use HasTrooperStamps;
}
