<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CompanyResource;
use App\Http\Traits\FileUploadTrait;
use App\Http\Traits\ResponseTrait;
use App\Models\V1\Company;
use App\Models\V1\Contact;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use ResponseTrait, FileUploadTrait;

    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['index', 'show']]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $companies = new Company();
        if ($request->has('search') && $request->get('search') !== '' && $request->get('search') !== null) {
            $search = $request->get('search');
            $words  = explode(' ', $search);
            $match  = '';
            foreach ($words as $word) {
                $match .= '+\"' . $word . '\" ';
            }
            $companies = $companies->WhereRaw("MATCH(name) AGAINST('$match' IN BOOLEAN MODE)");

            if ($companies->count() === 0) {

                return $this->successResponse('No company found with this name');
            }
        }
        $companies       = $companies->paginate(10);
        $CompanyResource = CompanyResource::collection($companies);

        return $this->successResponse('companiesList', $CompanyResource);
    }

    /**
     * @param int $companyId
     * @return JsonResponse
     */
    public function show(int $companyId): JsonResponse
    {
        $company = Company::find($companyId);
        if ($company === null) {

            return $this->failedResponse(404, 'The Company Not Found');
        }
        $company->load('contacts');
        $companyResource = CompanyResource::make($company);
        return $this->successResponse('showCompany', $companyResource);
    }

    /**
     * @param Request $request
     * @param int $companyId
     * @return JsonResponse
     */
    public function update(Request $request, int $companyId): JsonResponse
    {
        $checkCompany = $request->user('companies');

        if (!$checkCompany || $checkCompany->id !== $companyId) {
            return $this->failedResponse(403, 'You can not edit this company');
        }

        $company = Company::find($companyId);

        if ($company === null) {
            return $this->failedResponse(404, 'The Company Not Found');
        }

        $checkPassword = Hash::check($request->input('old_password'), $company->password);
        if (!$checkPassword) {
            return $this->failedResponse(422, 'Old Password Is Not Correct');
        }

        $certificate = $company->certificate;
        if ($request->hasFile('certificate')) {
            $file        = $request->file('certificate');
            $certificate = $this->uploadFile($file[0], 'certificate', 'certificate', 'public');
        }

        $company->update([
            'name'         => $request->input('name'),
            'admin'        => $request->input('admin'),
            'email'        => $request->input('email'),
            'certificate'  => $certificate,
            'phone_number' => $request->input('phone'),
            'password'     => Hash::make($request->input('password')),
        ]);

        $companyResource = new CompanyResource($company);

        return $this->successResponse('The Company Update Successfully', $companyResource);
    }

    /**
     * @param Request $request
     * @param int $companyId
     * @return JsonResponse
     */
    public function destroy(Request $request, int $companyId): JsonResponse
    {
        $company = Company::find($companyId);
        if ($company === null) {

            return $this->failedResponse(404, 'The Company Not Found');
        }
        $checkCompany = $request->user('companies');

        if (!$checkCompany || $checkCompany->id !== $companyId) {
            return $this->failedResponse(403, 'You can not edit this company');
        }

        Contact::whereCompanyId($companyId)->update(['company_id' => null]);
        $company->delete();

        return $this->successResponse('The Company Delete Successfully');
    }
}
