<?php

namespace App\Models;

use App\Enums\NoticeType;
use App\Models\Base\Notice as BaseNotice;
use App\Models\Concerns\HasTrooperStamps;
use App\Models\Scopes\HasNoticeScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notice extends BaseNotice
{
    use HasNoticeScopes;
    use HasFactory;
    use HasTrooperStamps;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts()
    {
        return array_merge($this->casts, [
            self::TYPE => NoticeType::class,
        ]);
    }
}
