<?php

namespace App\Models;

use App\Models\Base\Setting as BaseSetting;
use App\Models\Casts\LowerCast;
use App\Models\Concerns\HasTrooperStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends BaseSetting
{
    use HasFactory;
    use HasTrooperStamps;

    protected function casts()
    {
        return array_merge($this->casts, [
            self::KEY => LowerCast::class
        ]);
    }
}
