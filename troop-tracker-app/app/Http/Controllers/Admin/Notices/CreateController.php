<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Notices;

use App\Enums\NoticeType;
use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Organization;
use App\Services\BreadCrumbService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class CreateController
 *
 * Handles displaying the form to create a new notice.
 * @package App\Http\Controllers\Admin\Notices
 */
class CreateController extends Controller
{
    /**
     * CreateController constructor.
     *
     * @param BreadCrumbService $crumbs The service for managing breadcrumbs.
     */
    public function __construct(private readonly BreadCrumbService $crumbs)
    {
    }

    /**
     * Handle the request to display the notice creation page.
     *
     * Authorizes the user, sets up breadcrumbs, and returns the view containing
     * the form to create a new notice. If an organization_id is provided in the
     * query, it pre-selects that organization, ensuring the user has moderation
     * rights if they are not an administrator.
     *
     * @param Request $request The incoming HTTP request object.
     * @return View The rendered notice creation view.
     */
    public function __invoke(Request $request): View
    {
        $this->authorize('create', Notice::class);

        $this->crumbs->addRoute('Command Staff', 'admin.display');
        $this->crumbs->addRoute('Notices', 'admin.notices.list');
        $this->crumbs->add('Create');

        $notice = new Notice();

        $notice->organization_id = $request->query('organization_id');

        if ($notice->organization_id != null)
        {
            $trooper = Auth::user();

            $q = Organization::query();

            if (!$trooper->isAdministrator())
            {
                $q = $q->moderatedBy($trooper);
            }

            $notice->organization = $q->findOrFail($request->get('organization_id'));
        }

        $options = NoticeType::toDescriptions();

        $data = [
            'notice' => $notice,
            'options' => $options
        ];

        return view('pages.admin.notices.create', $data);
    }
}
