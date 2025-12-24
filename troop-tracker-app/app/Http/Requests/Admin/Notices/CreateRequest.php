<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Notices;

use App\Enums\NoticeType;
use App\Models\Notice;
use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Handles the validation for the user registration form.
 *
 * This class defines the base validation rules for user registration and dynamically
 * adds rules based on the organizations a user selects, including custom rules for
 * organization-specific identifiers and unit selections. It also customizes error messages
 * for a better user experience.
 */
class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Returns true as registration is open to guests.
     */
    public function authorize(): bool
    {
        //  we check NULL or PICKED in the rules
        return $this->user()->can('create', Notice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed> The combined validation rules for the registration form.
     */
    public function rules(): array
    {
        $rules = [
            Notice::TITLE => [
                'required',
                'string',
                'max:128',
            ],
            Notice::MESSAGE => [
                'required',
                'string',
            ],
            Notice::TYPE => [
                'required',
                'string',
                'max:16',
                'in:' . NoticeType::toValidator()
            ],
            Notice::STARTS_AT => ['required', 'date'],
            Notice::ENDS_AT => ['required', 'date', 'after:starts_at'],
        ];

        $trooper = $this->user();

        // Organization rules depend on role
        if ($trooper->isAdministrator())
        {
            // Admins can either leave it null or pick any existing org
            $rules[Notice::ORGANIZATION_ID] = [
                'nullable',
                Rule::exists(Organization::class, Organization::ID)
            ];
        }
        else
        {
            // Non-admins must supply an org they moderate
            $rules[Notice::ORGANIZATION_ID] = [
                'required',
                Rule::exists(Organization::class, Organization::ID)
                    ->whereIn('id', Organization::moderatedBy($trooper)->pluck('id')),
            ];
        }


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
}