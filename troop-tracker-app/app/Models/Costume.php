<?php

namespace App\Models;

use App\Models\Base\Costume as BaseCostume;
use App\Models\Concerns\HasTrooperStamps;
use App\Models\Scopes\HasCostumeScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Costume extends BaseCostume
{
    use HasCostumeScopes;
    use HasFactory;
    use HasTrooperStamps;

    public function fullCostumeName(): string
    {
        return "({$this->organization->name}) {$this->name}";
    }
}
