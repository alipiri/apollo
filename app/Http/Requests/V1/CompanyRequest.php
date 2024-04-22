<?php

namespace App\Http\Requests\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'                  => "required|min:3|unique:companies,name",
            'email'                 => "required|email|unique:companies,email",
            'phone'                 => "required|integer|unique:companies,phone_number",
            'admin'                 => 'required|min:3',
            'password'              => 'required|confirmed|min:3',
            'password_confirmation' => 'required|min:3',
            'certificate'           => 'nullable|image'
        ];
    }
}
