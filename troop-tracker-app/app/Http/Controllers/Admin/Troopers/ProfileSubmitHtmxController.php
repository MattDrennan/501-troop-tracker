<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Troopers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Troopers\ProfileRequest;
use App\Models\Trooper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;

/**
 * Class TrooperProfileSubmitHtmxController
 *
 * Handles the submission of trooper profile updates via an HTMX request.
 * @package App\Http\Controllers\Admin\Troopers
 */
class ProfileSubmitHtmxController extends Controller
{
    /**
     * Handle the incoming request to update a trooper's profile.
     *
     * Validates the request, updates the trooper's profile, and returns a view
     * fragment. If the membership role changes, it triggers a full page refresh
     * via an HTMX header. On successful update, it includes a flash message header.
     *
     * @param ProfileRequest $request The validated form request.
     * @param Trooper $trooper The trooper to be updated.
     * @return Response|View A response object containing the view and custom HTMX headers.
     */
    public function __invoke(ProfileRequest $request, Trooper $trooper): Response|View
    {
        try
        {
            $validated = $request->validateInputs();

            $trooper->name = $request->name;
            $trooper->email = $request->email;
            $trooper->phone = $request->phone;
            $trooper->membership_status = $request->membership_status;

            $trooper->save();

            $message = json_encode([
                'message' => 'Profile updated successfully!',
                'type' => 'success',
            ]);

            $data = [
                'trooper' => $trooper
            ];

            return response()
                ->view('pages.admin.troopers.profile', $data)
                ->header('X-Flash-Message', $message);
        }
        catch (ValidationException $e)
        {
            $errors = new ViewErrorBag();

            $errors->put('default', new MessageBag($e->errors()));

            view()->share('errors', $errors);

            $data = [
                'trooper' => $trooper
            ];

            $message = json_encode([
                'message' => 'Please fix the validation errors',
                'type' => 'danger',
            ]);

            return response()
                ->view('pages.admin.troopers.profile', $data)
                ->header('X-Flash-Message', $message);
        }
    }
}
