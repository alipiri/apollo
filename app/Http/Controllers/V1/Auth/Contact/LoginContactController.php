<?php

namespace App\Http\Controllers\V1\Auth\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginContactRequest;
use App\Http\Resources\V1\ContactResource;
use App\Http\Traits\ResponseTrait;
use App\Models\V1\Contact;
use Carbon\Carbon;
use Hash;
use Illuminate\Validation\ValidationException;

class LoginContactController extends Controller
{
    use ResponseTrait;

    public function __invoke(LoginContactRequest $request)
    {
        $contact = Contact::whereUsername($request->input('username'))->first();
        if (!$contact || !Hash::check($request->input('password'), $contact->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token              = $contact->createToken('apollo_token', [], Carbon::now()->addYear())->plainTextToken;
        $contact->api_token = $token;
        $contact->save();
        $contactResource = new ContactResource($contact);
        $data = ['contact' => $contactResource, 'token' => $token];

        return $this->successResponse('loginContact', $data);
    }
}
