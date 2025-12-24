<?php

namespace App\Rules\Admin\Organizations;

use App\Models\Organization;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validation rule to ensure an organization's name is unique among its siblings.
 *
 * This rule's behavior changes based on whether an organization is being created or updated.
 * - On creation, it checks for name uniqueness among the children of the given parent organization.
 * - On update, it checks for name uniqueness among siblings, excluding the organization being updated.
 */
class UniqueNameRule implements ValidationRule
{
    /**
     * Creates a new rule instance.
     *
     * @param bool $is_updating Indicates if the validation is for an update operation.
     * @param Organization $organization For creation, this is the parent. For updates, this is the organization being updated.
     */
    public function __construct(
        private bool $is_updating,
        private readonly Organization $organization)
    {
    }

    /**
     * Run the validation rule.
     *
     * Checks if the provided organization name already exists at the same hierarchical level.
     *
     * @param  string  $attribute The name of the attribute being validated.
     * @param  mixed  $value The value of the attribute being validated.
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The closure to call on validation failure.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!empty($value))
        {
            if ($this->is_updating)
            {
                //  updating
                $exists = $this->organization->parent->organizations()
                    ->where(Organization::ID, '!=', $this->organization->id)
                    ->where(Organization::NAME, $value)
                    ->exists();
            }
            else
            {
                //  creating
                $exists = $this->organization->organizations()
                    ->where(Organization::NAME, $value)
                    ->exists();
            }

            if ($exists)
            {
                $fail("{$this->organization->name} Name already exists.");
            }
        }
    }
}
