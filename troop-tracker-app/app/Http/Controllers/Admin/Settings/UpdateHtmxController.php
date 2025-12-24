<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class SettingsUpdateController
 *
 * Handles updating a specific site setting via an HTMX/AJAX request.
 * @package App\Http\Controllers\Admin\Settings
 */
class UpdateHtmxController extends Controller
{
    /**
     * Handle the incoming request to update a site setting.
     *
     * Authorizes the action, updates the setting's value from the request,
     * persists it to the database, and returns a simple 'ok' response with
     * an 'X-Flash-Message' header for HTMX to display a success message.
     *
     * @param Request $request The incoming HTTP request.
     * @param Setting $setting The setting model to be updated (via route model binding).
     * @return Response An HTTP response with a custom header.
     */
    public function __invoke(Request $request, Setting $setting): Response
    {
        $this->authorize('update', $setting);

        $value = $request->input('setting.' . $setting->key);

        $setting->value = $value;

        $setting->save();

        $message = json_encode([
            'message' => "Setting {$setting->key} successfully saved.",
            'type' => 'success',
        ]);

        return response('ok')->header('X-Flash-Message', $message);
    }
}
