<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\EventUpload;
use App\Models\Trooper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EventUploadTag
 * 
 * @property int $id
 * @property int $event_upload_id
 * @property int $trooper_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $created_id
 * @property int|null $updated_id
 * @property int|null $deleted_id
 * 
 * @property EventUpload $event_upload
 * @property Trooper $trooper
 *
 * @package App\Models\Base
 */
class EventUploadTag extends Model
{
    use SoftDeletes;
    const ID = 'id';
    const EVENT_UPLOAD_ID = 'event_upload_id';
    const TROOPER_ID = 'trooper_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
    const CREATED_ID = 'created_id';
    const UPDATED_ID = 'updated_id';
    const DELETED_ID = 'deleted_id';
    protected $table = 'tt_event_upload_tags';

    protected $casts = [
        self::ID => 'int',
        self::EVENT_UPLOAD_ID => 'int',
        self::TROOPER_ID => 'int',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::CREATED_ID => 'int',
        self::UPDATED_ID => 'int',
        self::DELETED_ID => 'int'
    ];

    protected $fillable = [
        self::EVENT_UPLOAD_ID,
        self::TROOPER_ID
    ];

    public function event_upload(): BelongsTo
    {
        return $this->belongsTo(EventUpload::class);
    }

    public function trooper(): BelongsTo
    {
        return $this->belongsTo(Trooper::class);
    }
}
