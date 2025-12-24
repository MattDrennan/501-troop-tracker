<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Troopers;

use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use App\Mail\TrooperApproved;
use App\Models\Trooper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

/**
 * Class TrooperApprovalSubmitHtmxController
 *
 * Handles the submission of a trooper's membership approval via an HTMX request.
 * This controller updates the trooper's status to 'Active', sends an approval email,
 * and returns a view fragment with a flash message in the response headers for HTMX to process.
 * @package App\Http\Controllers\Admin\Troopers
 */
class ApprovalSubmitHtmxController extends Controller
{
    /**
     * Handle the incoming request to approve a trooper's membership.
     *
     * This method authorizes the action, updates the trooper's status to 'Active',
     * saves the model, and dispatches an approval email. It returns a view
     * with a custom 'X-Flash-Message' header for HTMX to display a success message.
     *
     * @param Request $request The incoming HTTP request.
     * @param Trooper $trooper The trooper pending approval.
     * @return Response|View A response object containing the view and a custom header.
     */
    public function __invoke(Request $request, Trooper $trooper): Response|View
    {
        $this->authorize('approve', $trooper);

        $data = [
            'trooper' => $trooper
        ];

        $trooper->membership_status = MembershipStatus::Active;

        $trooper->save();

        Mail::to($trooper->email)->send(new TrooperApproved($trooper));

        $message = json_encode([
            'message' => "Trooper {$trooper->name} approved!",
            'type' => 'success',
        ]);

        return response()
            ->view('pages.admin.troopers.approval', $data)
            ->header('X-Flash-Message', $message);
    }
}
