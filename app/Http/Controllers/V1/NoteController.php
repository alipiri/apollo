<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\NoteResource;
use App\Http\Traits\ResponseTrait;
use App\Models\V1\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    use ResponseTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $contact = $request->user();
        if (!$contact){

            return $this->failedResponse(404, 'The contact not found');
        }
        $notes = Note::whereContactId($contact->id);

        if ($request->has('search') && $request->get('search') !== '' && $request->get('search') !== null){
            $notes = $notes->where('title', 'like', '%'.$request->get('search').'%');
            if ($notes->count() === 0){

                return $this->successResponse('No note found with this title');
            }
        }

        $notes = $notes->paginate(10);
        $notesResource = NoteResource::collection($notes);

        return $this->successResponse('notesList', $notesResource);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $contact = $request->user();
        if (!$contact){
            return $this->failedResponse(403, 'The contact not found');
        }
        if ($contact->is_ative === 0){
            return $this->failedResponse(403, 'Your account is not active');
        }

        $request->validate([
            'title' => 'required|min:3',
            'text' => 'required|min:3'
        ]);

        $note = Note::create([
            'contact_id' => $contact->id,
            'title' => $request->input('title'),
            'text' => $request->input('text')
        ]);

        if ($note){
            $note = $note->load('contact');
            $noteResource = new NoteResource($note);

            return $this->successResponse('storeNote', $noteResource);
        }

        return $this->failedResponse(422, 'Error in store note');
    }

    /**
     * @param Request $request
     * @param int $noteId
     * @return JsonResponse
     */
    public function update(Request $request, int $noteId): JsonResponse
    {
        $note = Note::find($noteId);
        if (!$note){
            return $this->failedResponse(404, 'The note not found');
        }

        $request->validate([
            'title' => 'required|min:3',
            'text' => 'required|min:3'
        ]);

        $contact = $request->user();
        $result = $note->update([
            'title' => $request->input('title'),
            'text' => $request->input('text')
        ]);

        if ($result){
            $note = $note->load('contact');
            $noteResource = new NoteResource($note);

            return $this->successResponse('storeNote', $noteResource);
        }

        return $this->failedResponse(422, 'Error in store note');
    }

    /**
     * @param Request $request
     * @param int $noteId
     * @return JsonResponse
     */
    public function destroy(Request $request, int $noteId): JsonResponse
    {
        $note = Note::find($noteId);
        $contact = $request->user();
        if ($note->contact_id !== $contact->id){
            return $this->failedResponse(403, 'You can not delete this note');
        }
        $note->delete();

        return $this->successResponse('The note delete successfully');
    }

}
