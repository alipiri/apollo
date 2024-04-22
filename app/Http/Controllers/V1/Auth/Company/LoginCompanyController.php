<?php

namespace App\Http\Controllers\V1\Auth\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginCompanyRequest;
use App\Http\Resources\V1\CompanyResource;
use App\Http\Traits\ResponseTrait;
use App\Models\V1\Company;
use Carbon\Carbon;
use Hash;
use Illuminate\Validation\ValidationException;

class LoginCompanyController extends Controller
{
    use ResponseTrait;

    public function __invoke(LoginCompanyRequest $request)
    {
        $company = Company::whereEmail($request->input('email'))->first();
        if (!$company || !Hash::check($request->input('password'), $company->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $company->createToken('apollo_token', [], Carbon::now()->addYear())->plainTextToken;
        $company->api_token = $token;
        $company->save();
        $company->load('contacts');
        $companyResource = CompanyResource::make($company);
        $data            = ['company' => $companyResource, 'token' => $token];

        return $this->successResponse('loginCompany', $data);
    }
}
