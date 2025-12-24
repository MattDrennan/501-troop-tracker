<?php

namespace App\Rules\Auth;

use App\Models\Organization;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validation rule to ensure a selected region is valid and active for a given organization.
 *
 * This rule checks if the provided region ID exists, is active, and is associated with the organization
 * specified in the constructor.
 */
class ValidRegionForOrganizationRule implements ValidationRule
{
    /**
     * Creates a new rule instance.
     *
     * @param Organization $organization The organization against which the region's validity will be checked.
     */
    public function __construct(private readonly Organization $organization)
    {
    }

    /**
     * Run the validation rule.
     *
     * Checks if the provided region ID exists and is active for the given organization.
     *
     * @param  string  $attribute The name of the attribute being validated.
     * @param  mixed  $value The value of the attribute (region ID).
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The closure to call on validation failure.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!empty($value))
        {
            $exists = $this->organization
                ->organizations()
                ->ofTypeRegions()
                ->where(Organization::ID, $value)
                ->exists();

            if (!$exists)
            {
                $fail('Region selection is invalid.');
            }
        }
    }
}
