<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\BreadCrumbService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class SettingsListController
 *
 * Handles the display of the site settings page in the admin section.
 * @package App\Http\Controllers\Admin\Settings
 */
class ListController extends Controller
{
    /**
     * SettingsListController constructor.
     *
     * @param BreadCrumbService $crumbs The breadcrumb service for managing navigation history.
     */
    public function __construct(private readonly BreadCrumbService $crumbs)
    {
    }

    /**
     * Handle the incoming request to display the site settings page.
     *
     * Authorizes the user, sets up breadcrumbs, fetches all settings from the
     * database, and returns the settings view.
     *
     * @param Request $request The incoming HTTP request.
     * @return View|RedirectResponse The rendered settings page view or a redirect response on authorization failure.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $this->authorize('viewAny', Setting::class);

        $this->crumbs->addRoute('Command Staff', 'admin.display');
        $this->crumbs->add('Site Settings');

        $settings = Setting::orderBy(Setting::KEY)->get();

        $data = [
            'settings' => $settings
        ];

        return view('pages.admin.settings.list', $data);
    }
}
