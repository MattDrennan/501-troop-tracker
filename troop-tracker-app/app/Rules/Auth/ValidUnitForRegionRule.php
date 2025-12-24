<?php

namespace App\Rules\Auth;

use App\Models\Organization;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validation rule to ensure a selected unit is valid and active for a given region.
 *
 * This rule checks if the provided unit ID exists, is active, and is associated with the region
 * specified in the constructor.
 */
class ValidUnitForRegionRule implements ValidationRule
{
    /**
     * Creates a new rule instance.
     *
     * @param Organization $region The region against which the unit's validity will be checked.
     */
    public function __construct(private readonly Organization $region)
    {
    }

    /**
     * Run the validation rule.
     *
     * Checks if the provided unit ID exists and is active for the given region.
     *
     * @param  string  $attribute The name of the attribute being validated.
     * @param  mixed  $value The value of the attribute (unit ID).
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The closure to call on validation failure.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!empty($value))
        {
            $exists = $this->region
                ->organizations()
                ->ofTypeUnits()
                ->where(Organization::ID, $value)
                ->exists();

            if (!$exists)
            {
                $fail('Unit selection is invalid.');
            }
        }
    }
}
