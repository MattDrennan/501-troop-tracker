<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Represents a single item (a "crumb") in a breadcrumb navigation trail.
 */
class BreadCrumb
{
    /**
     * Creates a new BreadCrumb instance.
     *
     * @param string $title The display text for the breadcrumb.
     * @param string $url The URL the breadcrumb links to. Defaults to an empty string for non-linked crumbs.
     */
    public function __construct(
        public readonly string $title,
        public readonly string $url = '')
    {
    }
}
