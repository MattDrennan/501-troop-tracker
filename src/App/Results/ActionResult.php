<?php

declare(strict_types=1);

namespace App\Results;

interface ActionResult
{
    public function send(): void;
}