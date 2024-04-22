<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ContactResource;
use App\Http\Resources\V1\NoteResource;
use App\Http\Traits\ResponseTrait;
use App\Models\V1\Company;
use App\Models\V1\Contact;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['index', 'show', 'changeStatus', 'AttachCompany']]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $contacts = new Contact();

        if ($request->has('company_id') && $request->get('company_id') !== '' && $request->get('company_id') !== null) {
            $companyId = $request->get('company_id');
            $company   = Company::find($companyId);
            if ($company === null) {

                return $this->failedResponse(404, 'The Company Not Found');
            }
            $contacts = $contacts->whereCompanyId($companyId);
        }
        if ($request->has('search') && $request->get('search') !== '' && $request->get('search') !== null) {
            $search = $request->get('search');
            $words  = explode(' ', $search);
            $match  = '';
            foreach ($words as $word) {
                $match .= '+\"' . $word . '\" ';
            }
            $contacts = $contacts->WhereRaw("MATCH(username) AGAINST('$match' IN BOOLEAN MODE)");

            if ($contacts->count() === 0) {

                return $this->successResponse('No contact found with this username');
            }
        }

        $contacts         = $contacts->paginate(10);
        $contactsResource = ContactResource::collection($contacts);

        return $this->successResponse('contactList', $contactsResource);
    }

    /**
     * @param int $contactId
     * @return JsonResponse
     */
    public function show(int $contactId): JsonResponse
    {
        $contact = Contact::with('company')->find($contactId);
        if ($contact === null) {

            return $this->failedResponse(404, 'The Contact Not Found');
        }
        $contactResource = new ContactResource($contact);

        return $this->successResponse('showContact', $contactResource);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changeStatus(Request $request): JsonResponse
    {
        $request->validate([
            'contact_id' => 'required|exists:contacts,id'
        ]);

        $contact            = Contact::find($request->input('contact_id'));
        $contact->is_active = !$contact->is_active;
        $contact->save();
        $message = $contact->is_active ? 'The Contact Activated' : 'The Contact Deactivated';

        return $this->successResponse($message);
    }

    /**
     * @param Request $request
     * @param int $contactId
     * @return JsonResponse
     */
    public function update(Request $request, int $contactId): JsonResponse
    {
        $checkContact = $request->user();

        if (!$checkContact || $checkContact->id !== $contactId) {
            return $this->failedResponse(403, 'You can not edit this contact');
        }

        $request->validate([
            'first_name'            => "required|min:2",
            'last_name'             => "required|min:2",
            'username'              => "required|min:5|unique:contacts,username,$contactId",
            'email'                 => "required|email|unique:contacts,email,$contactId",
            'password'              => "required|confirmed|min:3",
            'password_confirmation' => "required|min:3",
            'old_password'          => "required|min:3",
            'mobile_number'         => "nullable|unique:contacts,mobile_number,$contactId",
            'company_id'            => "nullable|exists:companies,id",
        ]);

        $contact = Contact::with('company')->find($contactId);
        if ($contact === null) {

            return $this->failedResponse(404, 'The Contact Not Found');
        }
        $checkPassword = Hash::check($request->input('old_password'), $contact->password);
        if (!$checkPassword) {
            return $this->failedResponse(422, 'The old password is not correct');
        }

        $result = $contact->update([
            'first_name'    => $request->input('first_name'),
            'last_name'     => $request->input('last_name'),
            'username'      => $request->input('username'),
            'email'         => $request->input('email'),
            'password'      => Hash::make($request->input('password')),
            'mobile_number' => $request->input('mobile_number'),
            'company_id'    => $request->input('company_id')
        ]);

        $contactResource = new ContactResource($contact);
        if ($result) {

            return $this->successResponse('The Contact Updated Successfully', $contactResource);
        }

        return $this->failedResponse(422, 'Error in Update Contact');
    }

    /**
     * @param int $contactId
     * @return JsonResponse
     */
    public function getNotes(int $contactId): JsonResponse
    {
        $contact = Contact::with(['company', 'notes'])->find($contactId);
        if ($contact === null) {

            return $this->failedResponse(404, 'The Contact Not Found');
        }
        $notesResource = NoteResource::collection($contact->notes);

        return $this->successResponse('contactsNotes', $notesResource);
    }

    /**
     * @param int $contactId
     * @param Request $request
     * @return JsonResponse
     */
    public function AttachCompany(int $contactId, Request $request): JsonResponse
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id'
        ]);

        $contact = Contact::without('company')->find($contactId);
        if ($contact === null) {

            return $this->failedResponse(404, 'The Contact Not Found');
        }

        $contact->company_id = $request->input('company_id');
        $contact->save();
        $contact->load('company');
        $contactResource = ContactResource::make($contact);

        return $this->successResponse('AttachCompany', $contactResource);
    }


    public function destroy(Request $request, int $contactId): JsonResponse
    {
        $contact = Contact::find($contactId);
        if ($contact === null) {

            return $this->failedResponse(404, 'The Contact Not Found');
        }
        $checkContact = $request->user();
        if ($checkContact->id !== $contact->id) {

            return $this->failedResponse(404, 'You can not delete this contact');
        }

        $contact->notes()->delete();
        $contact->delete();

        return $this->successResponse('The Contact deleted successfully');
    }
}
