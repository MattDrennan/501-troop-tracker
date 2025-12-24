<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\Organization;
use App\Models\Trooper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ActorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->admin();
        $this->makaze();    //moderator
    }

    private function admin()
    {
        $actor = Trooper::find(501) ?? new Trooper(['id' => 501]);

        $actor->name = 'Sith Lord';
        $actor->email = 'sith@galaxy-far-far-away.com';
        $actor->username = 'sith';
        $actor->password = Hash::make('password');
        $actor->membership_status = MembershipStatus::Active;
        $actor->membership_role = MembershipRole::Administrator;

        $actor->save();

        if ($actor->organizations->count() == 0)
        {
            $organization = Organization::firstWhere(Organization::NAME, '501st Legion');

            $actor->organizations()->attach($organization->id, [
                'identifier' => '99_999-1',
            ]);

            $unit = Organization::firstWhere(Organization::NAME, 'Makaze Squad');

            $actor->trooper_assignments()->create([
                'organization_id' => $unit->id,
                'member' => true,
                'moderator' => true
            ]);
        }
    }

    private function makaze()
    {
        $actor = Trooper::find(502) ?? new Trooper(['id' => 502]);

        $actor->name = 'Sith Lord Junior';
        $actor->email = 'sith-junior@galaxy-far-far-away.com';
        $actor->username = 'sith-jr';
        $actor->password = Hash::make('password');
        $actor->membership_status = MembershipStatus::Active;
        $actor->membership_role = MembershipRole::Moderator;

        $actor->save();

        if ($actor->organizations->count() == 0)
        {
            $organization = Organization::firstWhere(Organization::NAME, '501st Legion');

            $actor->organizations()->attach($organization->id, [
                'identifier' => '99_999-2',
            ]);

            $unit = Organization::firstWhere(Organization::NAME, 'Makaze Squad');

            $actor->trooper_assignments()->create([
                'organization_id' => $unit->id,
                'member' => true,
                'moderator' => true
            ]);
        }
    }
}