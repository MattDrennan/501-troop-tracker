<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Notices;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Notices\UpdateRequest;
use App\Models\Notice;
use App\Models\Organization;
use App\Services\FlashMessageService;
use Illuminate\Http\RedirectResponse;

/**
 * Class UpdateSubmitController
 *
 * Handles the submission of the form for updating an existing notice.
 * @package App\Http\Controllers\Admin\Notices
 */
class UpdateSubmitController extends Controller
{
    /**
     * UpdateSubmitController constructor.
     *
     * @param FlashMessageService $flash The service for displaying flash messages.
     */
    public function __construct(private readonly FlashMessageService $flash)
    {
    }

    /**
     * Handle the incoming request to update a notice.
     *
     * Validates the request, updates the notice's properties, saves it,
     * and then redirects with a success message.
     *
     * @param UpdateRequest $request The validated request containing the updated data.
     * @param Notice $notice The notice to be updated.
     * @return RedirectResponse A redirect response to the notices list.
     */
    public function __invoke(UpdateRequest $request, Notice $notice): RedirectResponse
    {
        $notice->title = $request->validated('title');
        $notice->type = $request->validated('type');
        $notice->starts_at = $request->validated('starts_at');
        $notice->ends_at = $request->validated('ends_at');
        $notice->message = $request->validated('message');

        $notice->save();

        $this->flash->updated($notice);

        return redirect()->route('admin.notices.list');
    }
}
