<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Notices;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\TrooperAssignment;
use App\Services\BreadCrumbService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class ListController
 *
 * Handles the display of the main notices list in the admin section.
 * This controller fetches and displays a list of active notices that the
 * authenticated user is authorized to see based on their moderation scope.
 * @package App\Http\Controllers\Admin\Notices
 */
class ListController extends Controller
{
    /**
     * ListController constructor.
     *
     * @param BreadCrumbService $crumbs The service for managing breadcrumbs.
     */
    public function __construct(private readonly BreadCrumbService $crumbs)
    {
    }

    /**
     * Handle the request to display the notices list page.
     *
     * Sets up breadcrumbs, retrieves active notices visible to the authenticated
     * user, and returns the list view.
     *
     * @param Request $request The incoming HTTP request object.
     * @return View The rendered notices list view.
     */
    public function __invoke(Request $request): View
    {
        $this->crumbs->addRoute('Command Staff', 'admin.display');
        $this->crumbs->add('Notices');

        $trooper = Auth::user();

        $q = Notice::active()->with([
            'organization.trooper_assignments' => function ($q) use ($trooper)
            {
                $q->where(TrooperAssignment::TROOPER_ID, $trooper->id);
            }
        ]);

        if (!$trooper->isAdministrator())
        {
            $q = $q->moderatedBy($trooper);
        }

        $data = [
            'notices' => $q->get()
        ];

        return view('pages.admin.notices.list', $data);
    }
}
