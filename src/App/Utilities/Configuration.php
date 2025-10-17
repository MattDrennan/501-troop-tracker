<?php

declare(strict_types=1);

namespace App\Utilities;

/**
 * A simple configuration
 * It holds all application settings and provides a way to access them.
 */
class Configuration
{
    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get a configuration value using dot notation.
     *
     * @param string $key The key to retrieve (e.g., 'db.host').
     * @param mixed|null $default The default value to return if the key is not found.
     * @return mixed The configuration value.
     */
    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->settings;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}