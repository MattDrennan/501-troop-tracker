<?php

namespace App\Models;

use App\Models\Base\EventUploadTag as BaseEventUploadTag;
use App\Models\Concerns\HasTrooperStamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventUploadTag extends BaseEventUploadTag
{
    use HasFactory;
    use HasTrooperStamps;
}
