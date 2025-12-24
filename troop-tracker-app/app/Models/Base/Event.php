<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Costume;
use App\Models\EventCostume;
use App\Models\EventOrganization;
use App\Models\EventTrooper;
use App\Models\EventUpload;
use App\Models\Organization;
use App\Models\Trooper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Event
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property bool $closed
 * @property int $charity_direct_funds
 * @property int $charity_indirect_funds
 * @property string|null $charity_name
 * @property int|null $charity_hours
 * @property bool $limit_participants
 * @property int|null $total_troopers_allowed
 * @property int|null $total_handlers_allowed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $created_id
 * @property int|null $updated_id
 * @property int|null $deleted_id
 * 
 * @property Collection|Costume[] $costumes
 * @property Collection|Organization[] $organizations
 * @property Collection|Trooper[] $troopers
 * @property Collection|EventUpload[] $event_uploads
 *
 * @package App\Models\Base
 */
class Event extends Model
{
    use SoftDeletes;
    const ID = 'id';
    const NAME = 'name';
    const STARTS_AT = 'starts_at';
    const ENDS_AT = 'ends_at';
    const CLOSED = 'closed';
    const CHARITY_DIRECT_FUNDS = 'charity_direct_funds';
    const CHARITY_INDIRECT_FUNDS = 'charity_indirect_funds';
    const CHARITY_NAME = 'charity_name';
    const CHARITY_HOURS = 'charity_hours';
    const LIMIT_PARTICIPANTS = 'limit_participants';
    const TOTAL_TROOPERS_ALLOWED = 'total_troopers_allowed';
    const TOTAL_HANDLERS_ALLOWED = 'total_handlers_allowed';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
    const CREATED_ID = 'created_id';
    const UPDATED_ID = 'updated_id';
    const DELETED_ID = 'deleted_id';
    protected $table = 'tt_events';

    protected $casts = [
        self::ID => 'int',
        self::STARTS_AT => 'datetime',
        self::ENDS_AT => 'datetime',
        self::CLOSED => 'bool',
        self::CHARITY_DIRECT_FUNDS => 'int',
        self::CHARITY_INDIRECT_FUNDS => 'int',
        self::CHARITY_HOURS => 'int',
        self::LIMIT_PARTICIPANTS => 'bool',
        self::TOTAL_TROOPERS_ALLOWED => 'int',
        self::TOTAL_HANDLERS_ALLOWED => 'int',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::CREATED_ID => 'int',
        self::UPDATED_ID => 'int',
        self::DELETED_ID => 'int'
    ];

    protected $fillable = [
        self::NAME,
        self::STARTS_AT,
        self::ENDS_AT,
        self::CLOSED,
        self::CHARITY_DIRECT_FUNDS,
        self::CHARITY_INDIRECT_FUNDS,
        self::CHARITY_NAME,
        self::CHARITY_HOURS,
        self::LIMIT_PARTICIPANTS,
        self::TOTAL_TROOPERS_ALLOWED,
        self::TOTAL_HANDLERS_ALLOWED
    ];

    public function costumes(): BelongsToMany
    {
        return $this->belongsToMany(Costume::class, 'tt_event_costumes')
                    ->withPivot(EventCostume::ID, EventCostume::REQUESTED, EventCostume::EXCLUDED, EventCostume::DELETED_AT, EventCostume::CREATED_ID, EventCostume::UPDATED_ID, EventCostume::DELETED_ID)
                    ->withTimestamps();
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'tt_event_organizations')
                    ->withPivot(EventOrganization::ID, EventOrganization::TROOPERS_ALLOWED, EventOrganization::HANDLERS_ALLOWED, EventOrganization::DELETED_AT, EventOrganization::CREATED_ID, EventOrganization::UPDATED_ID, EventOrganization::DELETED_ID)
                    ->withTimestamps();
    }

    public function troopers(): BelongsToMany
    {
        return $this->belongsToMany(Trooper::class, 'tt_event_troopers')
                    ->withPivot(EventTrooper::ID, EventTrooper::COSTUME_ID, EventTrooper::BACKUP_COSTUME_ID, EventTrooper::ADDED_BY_TROOPER_ID, EventTrooper::STATUS, EventTrooper::DELETED_AT, EventTrooper::CREATED_ID, EventTrooper::UPDATED_ID, EventTrooper::DELETED_ID)
                    ->withTimestamps();
    }

    public function event_uploads(): HasMany
    {
        return $this->hasMany(EventUpload::class);
    }
}
