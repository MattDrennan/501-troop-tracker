<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Organizations;

use App\Rules\Admin\Organizations\UniqueNameRule;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Handles the validation for the user registration form.
 *
 * This class defines the base validation rules for user registration and dynamically
 * adds rules based on the organizations a user selects, including custom rules for
 * organization-specific identifiers and unit selections. It also customizes error messages
 * for a better user experience.
 */
class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Returns true as registration is open to guests.
     */
    public function authorize(): bool
    {
        $organization = $this->route('organization');

        if ($organization == null)
        {
            throw new AuthorizationException('Organization not found or unauthorized.');
        }

        return $this->user()->can('update', $organization);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed> The combined validation rules for the registration form.
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:64',
                new UniqueNameRule(true, $this->route('organization'))
            ],
        ];

        return $rules;
    }

    public function validateInputs(): array
    {
        $validator = Validator::make($this->all(), $this->rules());

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }

        return $validator->validated();
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
                'phone' => preg_replace('/\D+/', '', $this->input('phone') ?? ''),
            ]);
        }
    }

    protected function failedValidation(ValidatorInterface $validator): void
    {
        //  avoids failing in HTMX
    }
}