<?php

declare(strict_types=1);

namespace App\Models\Observers;

use App\Models\Organization;

/**
 * Handles lifecycle events for the Organization model to manage hierarchical node paths.
 */
class OrganizationObserver
{
    /**
     * The separator used in the node path.
     */
    const SEP = ':';

    /**
     * Handle the Organization "saved" event.
     *
     * @param Organization $organization The organization instance that was saved.
     */
    public function saved(Organization $organization): void
    {
        $organization->node_path = $this->buildNodePath($organization);
        $organization->depth = substr_count($organization->node_path, self::SEP);

        $organization->saveQuietly();
    }

    /**
     * Builds a materialized path for the organization.
     *
     * This path represents the hierarchy of the organization by concatenating the IDs of its ancestors.
     *
     * @param Organization $organization The organization to generate the path for.
     * @return string The generated node path.
     */
    private function buildNodePath(Organization $organization): string
    {
        $node_path = [$organization->id];

        $node = $organization;

        while ($node = $node->parent) // assumes you defined a parent() relationship
        {
            // prepend each ancestor slug
            $node_path[] = $node->id;
        }

        // Reverse so root comes first, then join with dots
        return implode(self::SEP, array_reverse($node_path)) . self::SEP;
    }
}
