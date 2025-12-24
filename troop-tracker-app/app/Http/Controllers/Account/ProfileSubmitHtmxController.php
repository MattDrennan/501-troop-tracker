<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\ProfileRequest;
use App\Models\Trooper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;

/**
 * Handles the submission of the user profile form via an HTMX request.
 */
class ProfileSubmitHtmxController extends Controller
{
    /**
     * Handle the incoming request to update the user's profile.
     *
     * @param ProfileRequest $request The validated profile form request.
     * @return Response|View The rendered profile view with a flash message header, on success or validation failure.
     */
    public function __invoke(ProfileRequest $request): Response|View
    {
        $trooper = Trooper::findOrFail(Auth::user()->id);

        try
        {
            $validated = $request->validateInputs();

            $trooper->update($validated);

            $message = json_encode([
                'message' => 'Profile updated successfully!',
                'type' => 'success',
            ]);

            return response()
                ->view('pages.account.profile', $validated)
                ->header('X-Flash-Message', $message);
        }
        catch (ValidationException $e)
        {
            $errors = new ViewErrorBag();

            $errors->put('default', new MessageBag($e->errors()));

            view()->share('errors', $errors);

            $data = $request->only('name', 'email', 'phone');

            $message = json_encode([
                'message' => 'Please fix the validation errors',
                'type' => 'danger',
            ]);

            return response()
                ->view('pages.account.profile', $data)
                ->header('X-Flash-Message', $message);
        }
    }
}
