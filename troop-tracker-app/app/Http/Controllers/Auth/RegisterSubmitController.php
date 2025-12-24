<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Contracts\AuthenticationInterface;
use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Base\Region;
use App\Models\Organization;
use App\Models\Trooper;
use App\Models\TrooperAssignment;
use App\Models\TrooperOrganization;
use App\Models\TrooperRegion;
use App\Models\TrooperUnit;
use App\Models\Unit;
use App\Services\FlashMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

/**
 * Handles the submission of the user registration form.
 */
class RegisterSubmitController extends Controller
{
    /**
     * @param AuthenticationInterface $auth The authentication service.
     * @param FlashMessageService $flash The flash message service.
     */
    public function __construct(
        private readonly AuthenticationInterface $auth,
        private readonly FlashMessageService $flash,
    ) {
    }

    /**
     * Handle the incoming registration request.
     *
     * @param RegisterRequest $request The validated registration form request.
     * @return RedirectResponse A redirect response back to the registration page with a status message or errors.
     */
    public function __invoke(RegisterRequest $request): RedirectResponse
    {
        $auth_user_id = null;

        if (config('tracker.plugins.type') != 'standalone')
        {
            $auth_user_id = $this->auth->verify(
                username: $request->username,
                password: $request->password
            );

            if ($auth_user_id == null)
            {
                return back()
                    ->withInput()
                    ->withErrors(['username' => 'Invalid Credentials']);
            }
        }

        $this->register($request->validated(), $auth_user_id);

        $this->flash->success('Request submitted successfully! You will receive an e-mail when your request is approved or denied.');

        //  TODO FIX ROUTE TO SOMETHING THAT MAKES SENSE
        return redirect()->route('auth.register');
    }

    private function register(array $data, mixed $auth_user_id): Trooper
    {
        //  TODO optional identifier if handler
        $trooper = new Trooper();

        $trooper->name = $data['name'];
        $trooper->email = $data['email'];
        $trooper->phone = $data['phone'] ?? null;
        $trooper->username = $data['username'];
        $trooper->password = Hash::make($data['password']);
        $trooper->membership_role = $data['account_type'] == 'member' ? MembershipRole::Member : MembershipRole::Handler;

        $trooper->save();


        // Loop through selected organizations and assign identifiers
        foreach ($data['organizations'] ?? [] as $organization_id => $organization_data)
        {
            if (!empty($organization_data['selected']))
            {
                // You’ll need to map organization-specific fields to trooper columns
                // Example: if organization uses 'tkid' as identifier field
                $organization = Organization::find($organization_id);

                if ($organization)
                {
                    if (isset($organization_data['identifier']) && $organization_data['identifier'] != '')
                    {
                        $trooper_organization = new TrooperOrganization();

                        $trooper_organization->trooper_id = $trooper->id;
                        $trooper_organization->organization_id = $organization->id;
                        $trooper_organization->identifier = $organization_data['identifier'];
                        $trooper_organization->membership_status = MembershipStatus::Active;

                        $trooper_organization->save();
                    }

                    $organization_assignment = new TrooperAssignment();

                    $organization_assignment->trooper_id = $trooper->id;
                    $organization_assignment->organization_id = $organization->id;
                    $organization_assignment->notify = true;
                    $organization_assignment->member = $organization->organizations()->count() == 0;

                    $organization_assignment->save();

                    if (isset($organization_data['region_id']))
                    {
                        $region = $organization->organizations()
                            ->ofTypeRegions()
                            ->firstWhere(Organization::ID, $organization_data['region_id']);

                        $region_assignment = new TrooperAssignment();

                        $region_assignment->trooper_id = $trooper->id;
                        $region_assignment->organization_id = $region->id;
                        $region_assignment->notify = true;
                        $region_assignment->member = $region->organizations()->count() == 0;

                        $region_assignment->save();

                        if (isset($organization_data['unit_id']))
                        {
                            $unit = $region->organizations()
                                ->ofTypeUnits()
                                ->firstWhere(Organization::ID, $organization_data['unit_id']);

                            $unit_assignment = new TrooperAssignment();

                            $unit_assignment->trooper_id = $trooper->id;
                            $unit_assignment->organization_id = $unit->id;
                            $unit_assignment->notify = true;
                            $unit_assignment->member = true;

                            $unit_assignment->save();
                        }
                    }
                }
            }
        }

        return $trooper;
    }
}