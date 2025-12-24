<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Foundation\Http\FormRequest;

class HtmxFormRequest extends FormRequest
{
    protected function failedValidation(ValidatorInterface $validator): void
    {
        //  avoids failing in HTMX
    }
}