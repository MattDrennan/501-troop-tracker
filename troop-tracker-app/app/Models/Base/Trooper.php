<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Award;
use App\Models\Costume;
use App\Models\Event;
use App\Models\EventTrooper;
use App\Models\EventUpload;
use App\Models\EventUploadTag;
use App\Models\Organization;
use App\Models\TrooperAchievement;
use App\Models\TrooperAssignment;
use App\Models\TrooperAward;
use App\Models\TrooperCostume;
use App\Models\TrooperDonation;
use App\Models\TrooperOrganization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Trooper
 * 
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $username
 * @property string $password
 * @property Carbon|null $last_active_at
 * @property string $membership_status
 * @property string $membership_role
 * @property bool $instant_notification
 * @property bool $attendance_notification
 * @property bool $command_staff_notification
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Event[] $events
 * @property Collection|EventUploadTag[] $event_upload_tags
 * @property Collection|EventUpload[] $event_uploads
 * @property TrooperAchievement|null $trooper_achievement
 * @property Collection|TrooperAssignment[] $trooper_assignments
 * @property Collection|Award[] $awards
 * @property Collection|Costume[] $costumes
 * @property Collection|TrooperDonation[] $trooper_donations
 * @property Collection|Organization[] $organizations
 *
 * @package App\Models\Base
 */
class Trooper extends Model
{
    use SoftDeletes;
    const ID = 'id';
    const NAME = 'name';
    const PHONE = 'phone';
    const EMAIL = 'email';
    const EMAIL_VERIFIED_AT = 'email_verified_at';
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const LAST_ACTIVE_AT = 'last_active_at';
    const MEMBERSHIP_STATUS = 'membership_status';
    const MEMBERSHIP_ROLE = 'membership_role';
    const INSTANT_NOTIFICATION = 'instant_notification';
    const ATTENDANCE_NOTIFICATION = 'attendance_notification';
    const COMMAND_STAFF_NOTIFICATION = 'command_staff_notification';
    const REMEMBER_TOKEN = 'remember_token';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
    protected $table = 'tt_troopers';

    protected $casts = [
        self::ID => 'int',
        self::EMAIL_VERIFIED_AT => 'datetime',
        self::LAST_ACTIVE_AT => 'datetime',
        self::INSTANT_NOTIFICATION => 'bool',
        self::ATTENDANCE_NOTIFICATION => 'bool',
        self::COMMAND_STAFF_NOTIFICATION => 'bool',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime'
    ];

    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN
    ];

    protected $fillable = [
        self::NAME,
        self::PHONE,
        self::EMAIL,
        self::EMAIL_VERIFIED_AT,
        self::USERNAME,
        self::PASSWORD,
        self::LAST_ACTIVE_AT,
        self::MEMBERSHIP_STATUS,
        self::MEMBERSHIP_ROLE,
        self::INSTANT_NOTIFICATION,
        self::ATTENDANCE_NOTIFICATION,
        self::COMMAND_STAFF_NOTIFICATION,
        self::REMEMBER_TOKEN
    ];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'tt_event_troopers')
                    ->withPivot(EventTrooper::ID, EventTrooper::COSTUME_ID, EventTrooper::BACKUP_COSTUME_ID, EventTrooper::ADDED_BY_TROOPER_ID, EventTrooper::STATUS, EventTrooper::DELETED_AT, EventTrooper::CREATED_ID, EventTrooper::UPDATED_ID, EventTrooper::DELETED_ID)
                    ->withTimestamps();
    }

    public function event_upload_tags(): HasMany
    {
        return $this->hasMany(EventUploadTag::class);
    }

    public function event_uploads(): HasMany
    {
        return $this->hasMany(EventUpload::class);
    }

    public function trooper_achievement(): HasOne
    {
        return $this->hasOne(TrooperAchievement::class);
    }

    public function trooper_assignments(): HasMany
    {
        return $this->hasMany(TrooperAssignment::class);
    }

    public function awards(): BelongsToMany
    {
        return $this->belongsToMany(Award::class, 'tt_trooper_awards')
                    ->withPivot(TrooperAward::ID, TrooperAward::DELETED_AT, TrooperAward::CREATED_ID, TrooperAward::UPDATED_ID, TrooperAward::DELETED_ID)
                    ->withTimestamps();
    }

    public function costumes(): BelongsToMany
    {
        return $this->belongsToMany(Costume::class, 'tt_trooper_costumes')
                    ->withPivot(TrooperCostume::ID, TrooperCostume::DELETED_AT, TrooperCostume::CREATED_ID, TrooperCostume::UPDATED_ID, TrooperCostume::DELETED_ID)
                    ->withTimestamps();
    }

    public function trooper_donations(): HasMany
    {
        return $this->hasMany(TrooperDonation::class);
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'tt_trooper_organizations')
                    ->withPivot(TrooperOrganization::ID, TrooperOrganization::IDENTIFIER, TrooperOrganization::MEMBERSHIP_STATUS, TrooperOrganization::DELETED_AT, TrooperOrganization::CREATED_ID, TrooperOrganization::UPDATED_ID, TrooperOrganization::DELETED_ID)
                    ->withTimestamps();
    }
}
