<?php

namespace App\Http\Requests\Auth;

use App\Models\Trooper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            Trooper::USERNAME => [
                'required',
                Rule::exists(Trooper::class, Trooper::USERNAME),
            ],
            Trooper::PASSWORD => ['required'],
        ];
    }

    public function messages()
    {
        return [
            Trooper::USERNAME . '.required' => 'Username is required.',
            Trooper::USERNAME . '.exists' => 'This username does not exist in our records - do you need to setup your account?',
            Trooper::PASSWORD . '.required' => 'Password is required.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            Trooper::USERNAME => $this->input('username'),
            'remember_me' => $this->input('remember_me') === 'Y',
        ]);
    }
}
