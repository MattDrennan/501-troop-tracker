<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Notices;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Notices\CreateRequest;
use App\Models\Notice;
use App\Services\FlashMessageService;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;

/**
 * Class CreateSubmitController
 *
 * Handles the submission of the form for creating a new notice.
 * @package App\Http\Controllers\Admin\Notices
 */
class CreateSubmitController extends Controller
{
    /**
     * CreateSubmitController constructor.
     *
     * @param FlashMessageService $flash The service for displaying flash messages.
     */
    public function __construct(private readonly FlashMessageService $flash)
    {
    }

    /**
     * Handle the incoming request to create a new notice.
     *
     * Validates the request, creates a new notice with the provided data,
     * saves it, and then redirects with a success message.
     *
     * @param CreateRequest $request The validated request containing the new notice's data.
     * @return RedirectResponse A redirect response to the notices list.
     */
    public function __invoke(CreateRequest $request): RedirectResponse
    {
        $notice = new Notice();

        $notice->organization_id = $request->validated('organization_id');
        $notice->title = $request->validated('title');
        $notice->type = $request->validated('type');
        $notice->starts_at = $request->validated('starts_at');
        $notice->ends_at = $request->validated('ends_at');
        $notice->message = $request->validated('message');

        $notice->save();

        $this->flash->created($notice);

        return redirect()->route('admin.notices.list');
    }
}
