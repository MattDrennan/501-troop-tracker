<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions;

use App\Enums\MembershipStatus;
use App\Models\Base\Organization;
use App\Models\TrooperAssignment;
use App\Models\TrooperOrganization;
use Database\Seeders\Conversions\Traits\HasClubMaps;
use Database\Seeders\Conversions\Traits\HasEnumMaps;
use Database\Seeders\Conversions\Traits\HasSquadMaps;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrooperOrganizationSeeder extends Seeder
{
    use HasEnumMaps;
    use HasClubMaps;
    use HasSquadMaps;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy_troopers = DB::table('troopers')->get();

        foreach ($legacy_troopers as $trooper)
        {
            $this->assignOrganizationAndRegion($trooper);
            $this->assignUnit($trooper);
        }
    }

    private function assignOrganizationAndRegion($trooper)
    {
        $club_map = $this->getClubMap();

        foreach ($club_map as $column => $club)
        {
            $legacy_id = $club['legacy_id'];

            if (!isset($club['id']))
            {
                //  can't map to the new club
                continue;
            }

            $organization = $this->getOrganization($club['id']);

            $identifier = '';

            if ($club['identity'] != '')
            {
                $identifier = $trooper->{$club['identity']};
            }

            $has_identifier = $identifier != null && $identifier != '' && $identifier != '0';

            if ($has_identifier)
            {
                $this->loadTrooperOrganization($trooper, $organization, $identifier);
            }

            //* 0 = Regular Member, 1 = Super Admin, 2 = Moderator, 3 = RIP Member
            $moderator = $trooper->permissions == 2 && ($trooper->{$column} == 1 || $trooper->{$column} == 2);
            $member = $has_identifier;
            $notify = $trooper->{'esquad' . $legacy_id} == 1;

            if (!$notify && !$moderator && !$member)
            {
                //  not getting notified, skip
                continue;
            }

            $region = $organization->organizations->first();

            $this->loadTrooperAssignment($trooper->id, $organization->id, $notify, false, $moderator);
            $this->loadTrooperAssignment($trooper->id, $region->id, $notify, false, $moderator);
        }
    }

    private function assignUnit($trooper)
    {
        $squad_map = $this->getSquadMap();

        foreach ($squad_map as $legacy_id => $squad)
        {
            $notify = $trooper->{'esquad' . $legacy_id} == 1;

            $member = false;

            if ($trooper->squad == $legacy_id)
            {
                $member = true;
            }

            if (!$member && !$notify)
            {
                //  not a member and not notified
                continue;
            }

            $unit = $this->getOrganization($squad['id']);

            $region_assignment = TrooperAssignment::where(TrooperAssignment::ORGANIZATION_ID, $unit->parent_id)
                ->where(TrooperAssignment::TROOPER_ID, $trooper->id)
                ->first();

            $moderator = $region_assignment->moderator ?? false;

            $this->loadTrooperAssignment($trooper->id, $unit->id, $notify, $member, $moderator);
        }
    }

    private function loadTrooperOrganization($trooper, $organization, $identifier)
    {
        $to = TrooperOrganization::where(TrooperOrganization::TROOPER_ID, $trooper->id)
            ->where(TrooperOrganization::ORGANIZATION_ID, $organization->id)
            ->first();

        if ($to == null)
        {
            $to = new TrooperOrganization();

            $to->trooper_id = $trooper->id;
            $to->organization_id = $organization->id;
        }

        $to->membership_status = MembershipStatus::Active;
        $to->identifier = $identifier;

        $to->save();
    }

    private function loadTrooperAssignment($trooper_id, $organization_id, $notify, $member, $moderator)
    {
        $t = TrooperAssignment::where(TrooperAssignment::TROOPER_ID, $trooper_id)
            ->where(TrooperAssignment::ORGANIZATION_ID, $organization_id)
            ->first();

        if ($t == null)
        {
            $t = new TrooperAssignment();

            $t->trooper_id = $trooper_id;
            $t->organization_id = $organization_id;
        }

        $t->notify = $notify;
        $t->member = $member;
        $t->moderator = $moderator;

        $t->save();
    }

    private function getOrganization($id)
    {
        $organization = once(fn() => Organization::findOrFail($id));

        return $organization;
    }
}