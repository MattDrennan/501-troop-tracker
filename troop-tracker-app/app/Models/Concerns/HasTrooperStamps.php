<?php

namespace App\Models\Concerns;

use App\Models\Trooper;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait HasTrooperStamps
{
    public static function bootHasTrooperStamps(): void
    {
        static::creating(function ($model)
        {
            if (Auth::check())
            {
                $model->created_id = Auth::id();
                $model->updated_id = Auth::id();
            }
        });

        static::updating(function ($model)
        {
            if (Auth::check())
            {
                $model->updated_id = Auth::id();
            }
        });

        static::restoring(function ($model)
        {
            if (Auth::check())
            {
                $model->deleted_id = null;
            }
        });

        static::deleting(function ($model)
        {
            if (Auth::check() && $this->usingSoftDeletes())
            {
                $model->deleted_id = Auth::id();
            }
        });
    }

    /**
     * Get the user that created the model.
     *
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(Trooper::class, 'created_id', 'id');
    }

    /**
     * Get the user that edited the model.
     *
     * @return BelongsTo
     */
    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(Trooper::class, 'updated_id', 'id');
    }

    /**
     * Get the user that deleted the model.
     *
     * @return BelongsTo
     */
    public function deleted_by(): BelongsTo
    {
        return $this->belongsTo(Trooper::class, 'deleted_id', 'id');
    }

    /**
     * Has the model loaded the SoftDeletes trait.
     *
     * @return bool
     */
    private function usingSoftDeletes(): bool
    {
        return $usingSoftDeletes = in_array(
            'Illuminate\Database\Eloquent\SoftDeletes',
            class_uses_recursive(get_called_class())
        );
    }
}
