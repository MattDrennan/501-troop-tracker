<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Troopers;

use App\Enums\MembershipRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Troopers\AuthorityRequest;
use App\Models\Organization;
use App\Models\Trooper;
use App\Models\TrooperAssignment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;

/**
 * Class TrooperAuthoritySubmitHtmxController
 *
 * Handles the submission of a trooper's authority settings, including their
 * membership role and which organizations they moderate, via an HTMX request.
 * @package App\Http\Controllers\Admin\Troopers
 */
class AuthoritySubmitHtmxController extends Controller
{
    /**
     * Handle the incoming request to update a trooper's authority settings.
     *
     * Validates the request, updates the trooper's role, and syncs their
     * organization moderation assignments. Returns a view fragment with a
     * success or failure message in the `X-Flash-Message` header.
     *
     * @param AuthorityRequest $request The validated form request.
     * @param Trooper $trooper The trooper whose authorities are being updated.
     * @return Response|View A response object containing the view and custom HTMX headers.
     */
    public function __invoke(AuthorityRequest $request, Trooper $trooper): Response|View
    {
        try
        {
            $validated = $request->validateInputs();

            $trooper->membership_role = $request->validated('membership_role');

            $trooper->save();

            $this->updateModeratedOrganizations($request->validated('moderators', []), $trooper);

            $organization_authorities = Organization::withAllAssignments($trooper->id)->get();

            $data = [
                'trooper' => $trooper,
                'organization_authorities' => $organization_authorities,
            ];

            $message = json_encode([
                'message' => 'Authority updated successfully!',
                'type' => 'success',
            ]);

            return response()
                ->view('pages.admin.troopers.authority', $data)
                ->header('X-Flash-Message', $message);
        }
        catch (ValidationException $e)
        {
            $errors = new ViewErrorBag();

            $errors->put('default', new MessageBag($e->errors()));
            view()->share('errors', $errors);

            $organization_authorities = Organization::withAllAssignments($trooper->id)->get();

            $data = [
                'trooper' => $trooper,
                'organization_authorities' => $organization_authorities,
            ];

            $message = json_encode([
                'message' => 'Please fix the validation errors',
                'type' => 'danger',
            ]);

            return response()
                ->view('pages.admin.troopers.authority', $data)
                ->header('X-Flash-Message', $message);
        }
    }

    /**
     * Updates the organizations a trooper is assigned to moderate.
     *
     * @param array $moderated_organizations An array from the request, where keys are organization IDs.
     * @param Trooper $trooper The trooper whose assignments are being updated.
     * @return void
     */
    private function updateModeratedOrganizations(array $moderated_organizations, Trooper $trooper)
    {
        //  reset all the moderatored organizations to "false"
        $trooper->trooper_assignments()
            ->update([TrooperAssignment::MODERATOR => false]);

        //  not update ones that were selected as "true"
        foreach ($moderated_organizations as $organization_id => $data)
        {
            $selected = filter_var($data['selected'], FILTER_VALIDATE_BOOLEAN) == true;

            $trooper_assignment = $trooper->trooper_assignments()
                ->where(TrooperAssignment::ORGANIZATION_ID, $organization_id)
                ->first();

            if ($trooper_assignment == null)
            {
                $trooper_assignment = new TrooperAssignment();

                $trooper_assignment->organization_id = $organization_id;
                $trooper_assignment->trooper_id = $trooper->id;
            }

            $trooper_assignment->moderator = $selected;

            $trooper_assignment->save();
        }
    }
}
