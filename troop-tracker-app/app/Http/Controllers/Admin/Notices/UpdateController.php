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

/**
 * Class UpdateController
 *
 * Handles displaying the form to update an existing notice.
 * @package App\Http\Controllers\Admin\Notices
 */
class UpdateController extends Controller
{
    /**
     * UpdateController constructor.
     *
     * @param BreadCrumbService $crumbs The service for managing breadcrumbs.
     */
    public function __construct(private readonly BreadCrumbService $crumbs)
    {
    }

    /**
     * Handle the request to display the notice update page.
     *
     * Authorizes the user, sets up breadcrumbs, and returns the view
     * containing the form to update an existing notice.
     *
     * @param Request $request The incoming HTTP request object.
     * @param Notice $notice The notice to be updated.
     * @return View The rendered notice update view.
     */
    public function __invoke(Request $request, Notice $notice): View
    {
        $this->authorize('update', $notice);

        $this->crumbs->addRoute('Command Staff', 'admin.display');
        $this->crumbs->addRoute('Notices', 'admin.notices.list');
        $this->crumbs->add('Update');

        $options = NoticeType::toDescriptions();

        $data = [
            'notice' => $notice,
            'options' => $options,
        ];

        return view('pages.admin.notices.update', $data);
    }
}
