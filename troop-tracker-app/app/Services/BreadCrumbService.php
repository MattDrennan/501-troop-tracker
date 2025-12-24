<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Manages the creation and retrieval of a breadcrumb trail for navigation.
 */
class BreadCrumbService
{
    /**
     * An array of BreadCrumb objects representing the navigation trail.
     *
     * @var BreadCrumb[]
     */
    private array $trail = [];

    /**
     * Does a trail exist?
     *
     * @return bool yes or no
     */
    public function hasCrumbs(): bool
    {
        return !empty($this->trail);
    }

    /**
     * Retrieves the complete breadcrumb trail.
     *
     * @return BreadCrumb[] An array of BreadCrumb objects.
     */
    public function getCrumbs(): array
    {
        return $this->trail;
    }

    /**
     * Adds a new breadcrumb to the trail with just a title (no link).
     *
     * @param string $title The display text for the breadcrumb.
     */
    public function add(string $title): void
    {
        $crumb = new BreadCrumb($title);

        $this->trail[] = $crumb;
    }

    /**
     * Adds a new breadcrumb to the trail with a title and a link generated from a Laravel route.
     *
     * @param string $title The display text for the breadcrumb.
     * @param string $route The name of the route to generate the URL from.
     * @param array<string, mixed> $parms Optional parameters to pass to the route helper.
     */
    public function addRoute(string $title, string $route, array $parms = []): void
    {
        $url = route($route, $parms ?? []);

        $crumb = new BreadCrumb($title, $url);

        $this->trail[] = $crumb;
    }
}
