<?php

namespace App\Rules\Auth;

use App\Models\Organization;
use App\Models\TrooperOrganization;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validation rule to ensure an organization-specific identifier is unique among troopers of that organization.
 *
 * This rule checks the pivot table between troopers and organizations to verify that the
 * provided identifier (e.g., a member ID for a specific organization) is not already in use.
 */
class UniqueOrganizationIdentifierRule implements ValidationRule
{
    /**
     * Creates a new rule instance.
     *
     * @param Organization $organization The organization against which the identifier's uniqueness will be checked.
     */
    public function __construct(private readonly Organization $organization)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute The name of the attribute being validated.
     * @param  mixed  $value The value of the attribute being validated.
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The closure to call on validation failure.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!empty($value))
        {
            $exists = $this->organization->troopers()
                ->wherePivot(TrooperOrganization::IDENTIFIER, $value)
                ->exists();

            if ($exists)
            {
                $fail("{$this->organization->name} ID already exists.");
            }
        }
    }
}
