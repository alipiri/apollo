<?php

namespace App\Http\Controllers\V1\Auth\Company;

use App\Http\Controllers\Controller;
use App\Http\Traits\ResponseTrait;
use Illuminate\Http\Request;

class LogoutCompanyController extends Controller
{
    use ResponseTrait;

    public function __invoke(Request $request)
    {
        $company = $request->user('companies');
        $company->api_token = null;
        $company->save();
        $company->tokens()->delete();

        return $this->successResponse('The company logout');
    }
}
