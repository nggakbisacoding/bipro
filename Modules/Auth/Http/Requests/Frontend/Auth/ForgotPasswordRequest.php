<?php

namespace Modules\Auth\Http\Requests\Frontend\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Auth\Rules\UnusedPassword;
use LangleyFoxall\LaravelNISTPasswordRules\PasswordRules;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'max:255', 'email'],
            'password' => [
                'max:100',
                new UnusedPassword(request('email')),
                PasswordRules::changePassword(request('email'))
            ],
        ];
    }
}
