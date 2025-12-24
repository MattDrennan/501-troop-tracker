<?php

namespace App\Providers;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Services\BreadCrumbService;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use ValueError;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(BreadCrumbService::class, function ()
        {
            return new BreadCrumbService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        //  MIGRATION
        //
        $this->app->extend(MigrationRepositoryInterface::class, function ($repository, $app)
        {
            return new DatabaseMigrationRepository(
                $app['db'],
                'tt_migrations'
            );
        });

        //
        //  DATABASE MIGRATION MACRO
        //
        Blueprint::macro('trooperstamps', function ()
        {
            $this->unsignedBigInteger('created_id')->nullable();
            $this->unsignedBigInteger('updated_id')->nullable();
            $this->unsignedBigInteger('deleted_id')->nullable();
        });

        //
        //  BLADE BOOTS
        //
        Blade::if('role', function (MembershipRole|string|array $roles): bool
        {
            if (!Auth::check())
            {
                return false;
            }

            $user = Auth::user();

            if (!$user || $user->membership_status !== MembershipStatus::Active)
            {
                return false;
            }

            // Normalize roles into an array of MembershipRole enums
            if (is_string($roles))
            {
                $roles = array_map('trim', explode(',', $roles));
            }

            $normalized = collect($roles)->map(function ($role)
            {
                if ($role instanceof MembershipRole)
                {
                    return $role;
                }

                try
                {
                    return MembershipRole::from($role);
                }
                catch (ValueError $e)
                {
                    throw new InvalidArgumentException("Invalid permission role: '{$role}'");
                }
            });

            return $normalized->contains($user->membership_role);
        });
    }
}
