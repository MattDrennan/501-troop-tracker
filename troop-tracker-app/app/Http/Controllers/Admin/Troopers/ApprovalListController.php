<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Troopers;

use App\Enums\MembershipRole;
use App\Http\Controllers\Controller;
use App\Models\Trooper;
use App\Services\BreadCrumbService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class TrooperApprovalDisplayController
 *
 * Handles the display of troopers pending approval.
 * @package App\Http\Controllers\Admin\Troopers
 * This controller retrieves a list of troopers awaiting approval and displays them to authorized command staff.
 */
class ApprovalListController extends Controller
{
    /**
     * TrooperApprovalDisplayController constructor.
     *
     * @param BreadCrumbService $crumbs The breadcrumb service for managing navigation history.
     */
    public function __construct(private readonly BreadCrumbService $crumbs)
    {
    }

    /**
     * Handle the request to display the trooper approvals page.
     *
     * This method retrieves all troopers with a 'pending' status. For non-admin users,
     * it filters the list to show only troopers they are responsible for moderating.
     *
     * @param Request $request The incoming HTTP request.
     * @return View|RedirectResponse A view containing the list of troopers pending approval.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $this->crumbs->addRoute('Command Staff', 'admin.display');
        $this->crumbs->addRoute('Troopers', 'admin.troopers.list');
        $this->crumbs->add('Approvals');

        $trooper = Auth::user();

        $query = Trooper::pendingApprovals()->with('trooper_assignments.organization');

        if ($trooper->membership_role != MembershipRole::Administrator)
        {
            $query = $query->moderatedBy($trooper);
        }

        $troopers = $query->get();

        $data = [
            'troopers' => $troopers
        ];

        return view('pages.admin.troopers.approvals', $data);
    }
}
