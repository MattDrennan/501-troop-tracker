<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Event;
use App\Models\EventCostume;
use App\Models\EventTrooper;
use App\Models\Organization;
use App\Models\Trooper;
use App\Models\TrooperCostume;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Costume
 * 
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $created_id
 * @property int|null $updated_id
 * @property int|null $deleted_id
 * 
 * @property Organization $organization
 * @property Collection|Event[] $events
 * @property Collection|EventTrooper[] $event_troopers
 * @property Collection|Trooper[] $troopers
 *
 * @package App\Models\Base
 */
class Costume extends Model
{
    use SoftDeletes;
    const ID = 'id';
    const ORGANIZATION_ID = 'organization_id';
    const NAME = 'name';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
    const CREATED_ID = 'created_id';
    const UPDATED_ID = 'updated_id';
    const DELETED_ID = 'deleted_id';
    protected $table = 'tt_costumes';

    protected $casts = [
        self::ID => 'int',
        self::ORGANIZATION_ID => 'int',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::CREATED_ID => 'int',
        self::UPDATED_ID => 'int',
        self::DELETED_ID => 'int'
    ];

    protected $fillable = [
        self::ORGANIZATION_ID,
        self::NAME
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'tt_event_costumes')
                    ->withPivot(EventCostume::ID, EventCostume::REQUESTED, EventCostume::EXCLUDED, EventCostume::DELETED_AT, EventCostume::CREATED_ID, EventCostume::UPDATED_ID, EventCostume::DELETED_ID)
                    ->withTimestamps();
    }

    public function event_troopers(): HasMany
    {
        return $this->hasMany(EventTrooper::class);
    }

    public function troopers(): BelongsToMany
    {
        return $this->belongsToMany(Trooper::class, 'tt_trooper_costumes')
                    ->withPivot(TrooperCostume::ID, TrooperCostume::DELETED_AT, TrooperCostume::CREATED_ID, TrooperCostume::UPDATED_ID, TrooperCostume::DELETED_ID)
                    ->withTimestamps();
    }
}
