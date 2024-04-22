<?php

namespace App\Http\Controllers\V1\Auth\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ContactRequest;
use App\Http\Resources\V1\ContactResource;
use App\Http\Traits\ResponseTrait;
use App\Models\V1\Contact;
use Carbon\Carbon;
use Hash;

class RegisterContactController extends Controller
{
    use ResponseTrait;

    public function __invoke(ContactRequest $request)
    {
        $contact = Contact::create([
            'first_name'    => $request->input('first_name'),
            'last_name'     => $request->input('last_name'),
            'username'      => $request->input('username'),
            'email'         => $request->input('email'),
            'password'      => Hash::make($request->input('password')),
            'mobile_number' => $request->input('mobile_number'),
            'company_id'    => $request->input('company_id')
        ]);

        if ($contact) {

            $token              = $contact->createToken('apollo_token', [], Carbon::now()->addYear())->plainTextToken;
            $contact->api_token = $token;
            $contact->save();
            $contact->load('company');
            $contactResource = new ContactResource($contact);
            $data            = ['contact' => $contactResource, 'token' => $token];

            return $this->successResponse('registerContact', $data);
        }

        return $this->failedResponse(422, 'Error in register contact');
    }
}
