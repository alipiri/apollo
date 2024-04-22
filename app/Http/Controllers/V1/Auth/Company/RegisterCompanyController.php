<?php

namespace App\Http\Controllers\V1\Auth\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CompanyRequest;
use App\Http\Resources\V1\CompanyResource;
use App\Http\Traits\FileUploadTrait;
use App\Http\Traits\ResponseTrait;
use App\Models\V1\Company;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\JsonResponse;

class RegisterCompanyController extends Controller
{
    use FileUploadTrait, ResponseTrait;

    public function __invoke(CompanyRequest $request): JsonResponse
    {
        $certificate = null;
        if ($request->hasFile('certificate')) {
            $file        = $request->file('certificate');
            $certificate = $this->uploadFile($file[0], 'certificate', 'certificate', 'public');
        }

        $company = Company::create([
            'name'         => $request->input('name'),
            'admin'        => $request->input('admin'),
            'email'        => $request->input('email'),
            'certificate'  => $certificate,
            'phone_number' => $request->input('phone'),
            'password'     => Hash::make($request->input('password')),
        ]);

        if ($company) {
            $token = $company->createToken('apollo_token', [], Carbon::now()->addYear())->plainTextToken;;
            $company->api_token = $token;
            $company->save();

            $companyResource = new CompanyResource($company);
            $data            = ['company' => $companyResource, 'token' => $token];
            $company->createToken('apollo_token', [], Carbon::now()->addYear())->plainTextToken;

            return $this->successResponse('The Company Created Successfully', $data);
        }

        return $this->failedResponse(403, 'Error Creating Company');
    }
}
