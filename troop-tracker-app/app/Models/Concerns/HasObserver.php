<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;
use RuntimeException;

trait HasObserver
{
    /**
     * Bootstrap the trait.
     *
     * @return void
     */
    public static function bootHasObserver()
    {
        $name = Str::of(static::class)
            ->classBasename()
            ->value();

        $observerClass = "App\\Models\\Observers\\{$name}Observer";

        if (class_exists($observerClass))
        {
            static::observe($observerClass);
        }
        else
        {
            // Fail loudly so you know the observer wasn't found
            throw new RuntimeException(
                sprintf('Observer class [%s] not found for model [%s]', $observerClass, static::class)
            );
        }

    }
}
