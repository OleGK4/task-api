<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use App\Models\NoteTag;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Resources\NoteResource;
use App\Http\Resources\NoteCollection;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/notes",
     *     summary="Display a listing of the resource",
     *     @OA\Response(response="200", description="Notes paginated successfully", @OA\JsonContent(ref="#/components/schemas/NoteResource")),
     *     @OA\Response(response="404", description="No notes found."),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function index(Request $request)
    {
        $userNotes = $request->user()->notes()->paginate(5);

        if ($userNotes->count() > 0) {
            return new NoteCollection($userNotes);
        } else {
            return $this->respondWithError('No notes not found', 404, ['note' => 'No notes not found']);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/notes/filter",
     *     summary="Display a listing of the resource, ordered by date",
     *     @OA\Response(response="200", description="Notes paginated successfully", @OA\JsonContent(ref="#/components/schemas/NoteResource")),
     *     @OA\Response(response="404", description="No notes found."),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function filterByDate(Request $request)
    {
        $userNotes = $request->user()->notes()->orderBy('created_at', 'desc')->paginate(5);

        if ($userNotes->count() > 0) {
            return new NoteCollection($userNotes);
        } else {
            return $this->respondWithError('No notes not found', 404, ['note' => 'No notes not found']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/notes",
     *     summary="Store a newly created resource in storage",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="text", type="string"),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Note created successfully", @OA\JsonContent(ref="#/components/schemas/NoteResource")),
     *     @OA\Response(response="403", description="You do not own this note."),
     *     security={{"bearerAuth": {}}}
     * )
     */



    public function store(StoreNoteRequest $request)
    {
        $validated = $request->validated();
        $note = Note::create([
            'title' => $validated['title'],
            'text' => $validated['text'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'note' => $note
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/notes/{note}",
     *     summary="Display the specified note",
     *     @OA\Parameter(name="note", in="path", required=true, description="ID of the note", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Note retrieved successfully", @OA\JsonContent(ref="#/components/schemas/NoteResource")),
     *     @OA\Response(response="403", description="You do not own this note."),
     *     @OA\Response(response="404", description="Note not found."),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function show(Request $request)
    {
        $note = Note::find($request->note);
        if (empty($note)) {
            return $this->respondWithError('Note not found', 404, ['note' => 'Note not found']);
        }

        if ($request->user()->cannot('view', $note)) {
            return $this->respondWithError('You do not own this note', 403, ['ownership' => 'You do not own this note']);
        }
        return new NoteResource($note);
    }

    /**
     * @OA\Get(
     *     path="/api/notes/find/{search_tag}",
     *     summary="Search notes by tag or multiple tags",
     *     @OA\Parameter(name="search_tag", in="path", required=true, description="Tag or multiple tags to search by. Example: (personal1&tag2&family)", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="Notes found successfully", @OA\JsonContent(ref="#/components/schemas/NoteResource")),
     *     @OA\Response(response="404", description="No notes found."),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function showByTag(Request $request)
    {
        $searchTags = $request->search_tag;
        $searchTagsArray = explode('&', $searchTags); // Convert the tags into an array

        $notes = [];
        foreach ($searchTagsArray as $tag) {
            $tagQuery = Tag::where('name', 'LIKE', '%' . $tag . '%')->get();
            if (!empty($tagQuery[0])) {
                foreach ($tagQuery as $copyOfTag) {
                    $noteTag = NoteTag::where('tag_id', $copyOfTag->id)->first();
                    if (!empty($noteTag)) {
                        $note = $noteTag->note()->first();
                        array_push($notes, $note);
                    }
                }
            }
        }
        if (!empty($notes[0])){
            return new NoteCollection($notes);
        }
        return $this->respondWithError('Notes with tag(s) not found', 404, ['note' => 'Notes not found']);
    }



    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/notes/{note}",
     *     summary="Update the specified note",
     *     @OA\Parameter(name="note", in="path", required=true, description="ID of the note", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="text", type="string"),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Note updated successfully", @OA\JsonContent(ref="#/components/schemas/NoteResource")),
     *     @OA\Response(response="403", description="You do not own this note."),
     *     @OA\Response(response="404", description="Note not found."),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function update(UpdateNoteRequest $request)
    {
        $validated = $request->validated();
        $note = Note::find($request->note);

        if (empty($note)) {
            return $this->respondWithError('Note not found', 404, ['note' => 'Note not found']);
        }

        if ($request->user()->cannot('update', $note)) {
            return $this->respondWithError('You do not own this note', 403, ['ownership' => 'You do not own this note']);
        }

        if ($request->filled('title')) {
            $note->update([
                'title' => $validated['title'],
            ]);
        }
        if ($request->filled('text')) {
            $note->update([
                'text' => $validated['text'],
            ]);
        }

        return response()->json([
            new NoteResource($note)
        ]);
    }



    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/notes/{note}",
     *     summary="Remove the specified note",
     *     @OA\Parameter(name="note", in="path", required=true, description="ID of the note", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Note deleted successfully"),
     *     @OA\Response(response="403", description="You do not own this note."),
     *     @OA\Response(response="404", description="Note not found."),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function destroy(Request $request)
    {
        $note = Note::find($request->note);
        if (empty($note)) {
            return $this->respondWithError('Note not found', 404, ['note' => 'Note not found']);
        }

        if ($request->user()->cannot('delete', $note)) {
            return $this->respondWithError('You do not own this note', 403, ['ownership' => 'You do not own this note']);
        }

        if (!empty($note->noteTags())) {
            $note->noteTags()->delete();
        }

        $note->delete();
        return response()->json([
            'message' => 'Note deleted'
        ], 200);
    }
}
