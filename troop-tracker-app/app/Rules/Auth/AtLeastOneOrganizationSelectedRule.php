<?php

namespace App\Rules\Auth;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validation rule to ensure that at least one organization is selected from an array of organizations.
 *
 * This rule is typically used on a form where a user must choose at least one
 * organization membership. It expects the input value to be an array where each item
 * represents a organization and has a 'selected' key.
 */
class AtLeastOneOrganizationSelectedRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * Checks if the given value is an array and if at least one element
     * within it has a truthy 'selected' property.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The closure to call on validation failure.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value) || !collect($value)->contains(fn($organization) => !empty($organization['selected'])))
        {
            $fail('Please select at least one organization.');
        }
    }
}
