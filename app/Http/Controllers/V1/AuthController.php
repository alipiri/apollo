<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\AuthCompanyRequest;
use App\Models\V1\Company;
use App\Models\V1\Contact;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{


    public function companyLogin(AuthCompanyRequest $request)
    {
        $company = Company::whereEmail($request->input('email'))->first();
        if (!$company || !Hash::check($request->input('password'), $company->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $company->createToken('apollo_token', [], Carbon::now()->addYear())->plainTextToken;
    }

    public function contactLogin(Request $request)
    {
        $contact = Contact::whereUsername($request->input('username'))->first();
        if (!$contact || !Hash::check($request->input('password'), $contact->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $contact->createToken('apollo_token', [], Carbon::now()->addYear())->plainTextToken;
    }

}
