<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Defines the hierarchical types of organizational structures.
 */
enum OrganizationType: string
{
    use HasEnumHelpers;

    /**
     * The top-level entity, like '501st Legion'.
     */
    case Organization = 'organization';

    /**
     * A regional subdivision of an organization, like a 'Garrison'.
     */
    case Region = 'region';

    /**
     * A local subdivision of a region, like a 'Squad'.
     */
    case Unit = 'unit';
}