<?php

declare(strict_types=1);

namespace App\Actions;

use App\Results\ActionResult;

interface ActionInterface
{
    public function execute(): ActionResult;
}