<?php

namespace App\Models;

use App\Models\Base\EventUpload as BaseEventUpload;
use App\Models\Concerns\HasTrooperStamps;
use App\Models\Scopes\HasEventUploadScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventUpload extends BaseEventUpload
{
    use HasEventUploadScopes;
    use HasFactory;
    use HasTrooperStamps;
}
