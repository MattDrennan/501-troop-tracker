<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\Trooper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrooperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy_troopers = DB::table('troopers')->get();

        foreach ($legacy_troopers as $trooper)
        {
            $t = Trooper::find($trooper->id) ?? new Trooper(['id' => $trooper->id]);

            $t->name = $trooper->name;
            $t->phone = $trooper->phone;
            $t->username = $trooper->forum_id;
            $t->email = $trooper->email ?? '^' . uniqid();
            $t->password = $trooper->password ?? '^' . uniqid();

            $t->last_active_at = $trooper->last_active;
            $t->created_at = $trooper->datecreated;

            $t->instant_notification = $trooper->efast;
            $t->attendance_notification = $trooper->econfirm;
            $t->command_staff_notification = $trooper->ecommandnotify;

            if ($trooper->approved)
            {
                $t->membership_status = match ((int) $trooper->permissions)
                {
                    0 => MembershipStatus::Active,
                    // 1 => MembershipStatus::Admin,
                    // 2 => MembershipStatus::Moderator,
                    3 => MembershipStatus::Retired,
                    default => MembershipStatus::Active,
                };
            }
            else
            {
                $t->membership_status = MembershipStatus::Pending;
            }

            $t->membership_role = match ((int) $trooper->permissions)
            {
                0 => MembershipRole::Member,
                1 => MembershipRole::Administrator,
                2 => MembershipRole::Moderator,
                //3 => MembershipRole::Retired,
                4 => MembershipRole::Handler,
                default => MembershipRole::Member,
            };

            $t->save();
        }
    }
}