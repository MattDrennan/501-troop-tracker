<?php

namespace App\Utilities;

use \ReflectionClass;
use \ReflectionProperty;

trait PayloadableTrait
{
    /**
     * Converts ONLY the protected properties defined on the using class 
     * (excluding the trait's own properties) into an array.
     *
     * @return array An associative array of the protected properties and their values.
     */
    public function getDataPayload(mixed $exclude = null): array
    {
        $args = func_get_args();
        $exclusions = [];

        // If $exclude is passed as a string or array, process it first.
        if (is_string($exclude)) {
            $exclusions[] = $exclude;
        } elseif (is_array($exclude)) {
            $exclusions = array_merge($exclusions, $exclude);
        }

        // Process any remaining arguments (if user passed multiple strings like ->getDataPayload('name', 'email'))
        // Note: The first argument is already in $exclude, so we start from index 1.
        for ($i = 1; $i < count($args); $i++) {
            if (is_string($args[$i])) {
                $exclusions[] = $args[$i];
            }
        }

        $data = [];

        $reflection = new ReflectionClass($this);

        // The class name using the trait (e.g., 'User')
        $className = get_class($this);

        foreach ($reflection->getProperties(ReflectionProperty::IS_PROTECTED) as $property) {
            // CRUCIAL STEP: Check if the property was DEFINED in the current class 
            // and not inherited or brought in by a trait property (like $traitData).
            // This is the cleanest way to exclude properties brought in by the trait.
            if ($property->getDeclaringClass()->getName() === $className) {
                // Get the value (requires setting accessible if not public)
                $property->setAccessible(true);
                $nm = $property->getName();

                if (!isset($exclusions[$nm])) {
                    $data[$nm] = $property->getValue($this);
                }
            }
        }

        return $data;
    }
}