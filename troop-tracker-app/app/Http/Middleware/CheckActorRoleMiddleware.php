<?php

namespace App\Http\Middleware;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use ValueError;

class CheckActorRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (count($roles) > 0)
        {
            $user = Auth::user();

            if (!$user)
            {
                abort(401, 'Unauthorized.');
            }

            foreach ($roles as $role)
            {
                if (is_string($role))
                {
                    try
                    {
                        $role = MembershipRole::from($role);
                    }
                    catch (ValueError $e)
                    {
                        throw new InvalidArgumentException("Invalid MembershipRole: '{$role}'");
                    }
                }

                if ($user->membership_status == MembershipStatus::Active && $user->membership_role === $role)
                {
                    return $next($request);
                }
            }

            //  don't match any role - now - do we?!
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }

}
