<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Trooper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TrooperAchievement
 * 
 * @property int $id
 * @property int $trooper_id
 * @property int|null $trooper_rank
 * @property bool $trooped_all_squads
 * @property bool $first_troop_completed
 * @property bool $trooped_10
 * @property bool $trooped_25
 * @property bool $trooped_50
 * @property bool $trooped_75
 * @property bool $trooped_100
 * @property bool $trooped_150
 * @property bool $trooped_200
 * @property bool $trooped_250
 * @property bool $trooped_300
 * @property bool $trooped_400
 * @property bool $trooped_500
 * @property bool $trooped_501
 * @property float $volunteer_hours
 * @property float $direct_funds
 * @property float $indirect_funds
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Trooper $trooper
 *
 * @package App\Models\Base
 */
class TrooperAchievement extends Model
{
    use SoftDeletes;
    const ID = 'id';
    const TROOPER_ID = 'trooper_id';
    const TROOPER_RANK = 'trooper_rank';
    const TROOPED_ALL_SQUADS = 'trooped_all_squads';
    const FIRST_TROOP_COMPLETED = 'first_troop_completed';
    const TROOPED_10 = 'trooped_10';
    const TROOPED_25 = 'trooped_25';
    const TROOPED_50 = 'trooped_50';
    const TROOPED_75 = 'trooped_75';
    const TROOPED_100 = 'trooped_100';
    const TROOPED_150 = 'trooped_150';
    const TROOPED_200 = 'trooped_200';
    const TROOPED_250 = 'trooped_250';
    const TROOPED_300 = 'trooped_300';
    const TROOPED_400 = 'trooped_400';
    const TROOPED_500 = 'trooped_500';
    const TROOPED_501 = 'trooped_501';
    const VOLUNTEER_HOURS = 'volunteer_hours';
    const DIRECT_FUNDS = 'direct_funds';
    const INDIRECT_FUNDS = 'indirect_funds';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
    protected $table = 'tt_trooper_achievements';

    protected $casts = [
        self::ID => 'int',
        self::TROOPER_ID => 'int',
        self::TROOPER_RANK => 'int',
        self::TROOPED_ALL_SQUADS => 'bool',
        self::FIRST_TROOP_COMPLETED => 'bool',
        self::TROOPED_10 => 'bool',
        self::TROOPED_25 => 'bool',
        self::TROOPED_50 => 'bool',
        self::TROOPED_75 => 'bool',
        self::TROOPED_100 => 'bool',
        self::TROOPED_150 => 'bool',
        self::TROOPED_200 => 'bool',
        self::TROOPED_250 => 'bool',
        self::TROOPED_300 => 'bool',
        self::TROOPED_400 => 'bool',
        self::TROOPED_500 => 'bool',
        self::TROOPED_501 => 'bool',
        self::VOLUNTEER_HOURS => 'float',
        self::DIRECT_FUNDS => 'float',
        self::INDIRECT_FUNDS => 'float',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime'
    ];

    protected $fillable = [
        self::TROOPER_ID,
        self::TROOPER_RANK,
        self::TROOPED_ALL_SQUADS,
        self::FIRST_TROOP_COMPLETED,
        self::TROOPED_10,
        self::TROOPED_25,
        self::TROOPED_50,
        self::TROOPED_75,
        self::TROOPED_100,
        self::TROOPED_150,
        self::TROOPED_200,
        self::TROOPED_250,
        self::TROOPED_300,
        self::TROOPED_400,
        self::TROOPED_500,
        self::TROOPED_501,
        self::VOLUNTEER_HOURS,
        self::DIRECT_FUNDS,
        self::INDIRECT_FUNDS
    ];

    public function trooper(): BelongsTo
    {
        return $this->belongsTo(Trooper::class);
    }
}
