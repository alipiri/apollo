<?php

namespace App\Http\Controllers\V1\Auth\Contact;

use App\Http\Controllers\Controller;
use App\Http\Traits\ResponseTrait;
use Illuminate\Http\Request;

class LogoutContactController extends Controller
{
    use ResponseTrait;

    public function __invoke(Request $request)
    {
        $contact = $request->user('contacts');
        $contact->api_token = null;
        $contact->save();
        $contact->tokens()->delete();

        return $this->successResponse('The contact logout');
    }
}
