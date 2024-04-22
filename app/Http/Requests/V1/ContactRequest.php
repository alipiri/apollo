<?php

namespace App\Http\Requests\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'first_name'            => 'required|min:2',
            'last_name'             => 'required|min:2',
            'username'              => 'required|min:5|unique:contacts,username',
            'email'                 => 'required|email|unique:contacts,email',
            'password'              => 'required|confirmed|min:3',
            'password_confirmation' => 'required|min:3',
            'mobile_number'         => 'nullable|unique:contacts,mobile_number',
            'company_id'            => 'nullable|exists:companies,id'
        ];
    }
}
