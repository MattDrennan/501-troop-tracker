<?php

namespace App\Http\Requests\Auth;

use App\Models\Organization;
use App\Models\Trooper;
use App\Rules\Auth\AtLeastOneOrganizationSelectedRule;
use App\Rules\Auth\UniqueOrganizationIdentifierRule;
use App\Rules\Auth\ValidRegionForOrganizationRule;
use App\Rules\Auth\ValidUnitForRegionRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * Handles the validation for the user registration form.
 *
 * This class defines the base validation rules for user registration and dynamically
 * adds rules based on the organizations a user selects, including custom rules for
 * organization-specific identifiers and region selections. It also customizes error messages
 * for a better user experience.
 */
class RegisterRequest extends FormRequest
{
    private ?Collection $organizations = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Returns true as registration is open to guests.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed> The combined validation rules for the registration form.
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:240'],
            'phone' => ['nullable', 'string', 'max:10'],
            'account_type' => ['required', 'in:member,handler'],
            'username' => [
                'required',
                'string',
                Rule::unique('tt_troopers', Trooper::USERNAME),
            ],
            'password' => ['required', 'string'],
        ];

        $rules = array_merge($rules, $this->getOrganizationValidationRules());

        return $rules;
    }

    /**
     * Prepare the data for validation.
     *
     * This method sanitizes the phone number by removing any non-digit characters.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('phone'))
        {
            $this->merge([
                'phone' => preg_replace('/\D+/', '', $this->input('phone')),
            ]);
        }
    }

    /**
     * Generates dynamic validation rules for selected organizations.
     *
     * Fetches active organizations and constructs validation rules for their specific identifiers
     * (e.g., TKID, CAT #) and associated regions, applying custom rule objects.
     *
     * @return array<string, mixed> An array of validation rules for the 'organizations' input.
     */
    private function getOrganizationValidationRules(): array
    {
        $rules = [
            'organizations' => ['array', new AtLeastOneOrganizationSelectedRule()],
            'organizations.*.selected' => ['nullable', 'boolean'],
        ];

        $organizations = $this->getOrganizations();

        foreach ($organizations as $organization)
        {
            if (!empty($organization->identifier_validation))
            {
                $organization_rules = explode('|', $organization->identifier_validation);

                $organization_rules[] = "required_if:organizations.{$organization->id}.selected,1";
                $organization_rules[] = new UniqueOrganizationIdentifierRule($organization);

                $rules["organizations.{$organization->id}.identifier"] = $organization_rules;
            }

            $regions = $organization->organizations;

            if ($regions->count() > 0)
            {
                $rules["organizations.{$organization->id}.region_id"] = [
                    "required_if:organizations.{$organization->id}.selected,1",
                    new ValidRegionForOrganizationRule($organization)
                ];

                foreach ($regions as $region)
                {
                    $units = $region->organizations;

                    if ($units->count() > 0)
                    {
                        $rules["organizations.{$organization->id}.unit_id"] = [
                            "required_if:organizations.{$organization->id}.regions.{$region->id}.selected,1",
                            new ValidUnitForRegionRule($region)
                        ];
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     *
     * This method is used to add custom, user-friendly error messages for the
     * dynamically generated organization identifier rules.
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator): void
    {
        $active_organizations = $this->getOrganizations();

        $messages = [];

        foreach ($active_organizations as $organization)
        {
            $key = "organizations.{$organization->id}.identifier";

            if (!empty($organization->identifier_validation))
            {
                $rules = explode('|', $organization->identifier_validation);

                foreach ($rules as $rule)
                {
                    $ruleName = $this->normalizeRuleKey($rule);

                    $messages["{$key}.{$ruleName}"] = "The {$organization->identifier_display} for {$organization->name} must be {$this->friendlyPhrase($rule)}.";
                }

                if ($organization->organizations->count() > 0)
                {
                    $messages["organizations.{$organization->id}.region_id.required_if"] = "Please select a region for {$organization->name} if you’ve chosen it.";
                }

                // Optional: add a message for the required_if rule
                $messages["{$key}.required_if"] = "Please enter your {$organization->identifier_display} for {$organization->name} if selected.";
            }
        }

        $validator->setCustomMessages($messages);
    }

    private function normalizeRuleKey(string $rule): string
    {
        // Laravel uses 'between' not 'between:1000,9999' for message keys
        return explode(':', $rule)[0];
    }

    private function friendlyPhrase(string $rule): string
    {
        return match ($rule)
        {
            'integer' => 'an integer',
            'string' => 'a valid string',
            default => str_replace(':', ' ', $rule),
        };
    }

    private function getOrganizations(): Collection
    {
        if (!isset($this->organizations))
        {
            $this->organizations = Organization::fullyLoaded()->get();
        }
        return $this->organizations;
    }
}