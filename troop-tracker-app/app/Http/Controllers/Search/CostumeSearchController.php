<?php

declare(strict_types=1);

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Models\Base\Organization;
use App\Models\Costume;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles AJAX search requests for costumes within a specific organization.
 *
 * This controller is designed to be used by autocomplete or typeahead components,
 * providing a JSON response with costumes that match a given search query.
 */
class CostumeSearchController extends Controller
{
    /**
     * Handle the incoming request to search for costumes.
     *
     * It filters costumes by organization ID and a name-based search query,
     * returning a limited set of results as a JSON response.
     *
     * @param Request $request The incoming HTTP request, expecting 'query' parameters.
     * @return JsonResponse A JSON response containing an array of matching costumes.
     */
    public function __invoke(Request $request, Organization $organization): JsonResponse
    {
        $query = $request->get('query', '');
        $limit = max((int) $request->get('limit', 10), 100);

        $results = $organization->costumes()
            ->where(Costume::NAME, 'like', '%' . $query . '%')
            ->orderBy(Costume::NAME)
            ->limit($limit)
            ->get(['id', 'name']);

        return response()->json($results);
    }
}
