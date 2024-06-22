<?php

namespace Modules\Auth\Http\Requests\Frontend\Auth;

use Illuminate\Foundation\Http\FormRequest;
use LangleyFoxall\LaravelNISTPasswordRules\PasswordRules;
use Illuminate\Validation\Rule;
use Modules\Auth\Rules\Captcha;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => array_merge(['max:100'], PasswordRules::register($data['email'] ?? null)),
            'terms' => ['required', 'in:1'],
            'g-recaptcha-response' => ['required_if:captcha_status,true', new Captcha],
        ];
    }

    public function attributes()
    {
        return [
            'terms' => __('You must accept the Terms & Conditions.'),
            'g-recaptcha-response' => __('validation.required', ['attribute' => 'captcha']),
        ];
    }
}
