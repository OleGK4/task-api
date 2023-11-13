<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreNoteTagRequest;
use App\Http\Resources\NoteTagCollection;
use App\Http\Resources\NoteTagResource;
use App\Models\Note;
use App\Models\NoteTag;
use App\Models\Tag;
use Illuminate\Http\Request;

class NoteTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/notes/{note}/tags",
     *     summary="Display tags associated with the specified note",
     *     @OA\Parameter(name="note", in="path", required=true, description="ID of the note", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Note tags paginated successfully", @OA\JsonContent(ref="#/components/schemas/NoteTagResource")),
     *     @OA\Response(response="403", description="You do not own this note."),
     *     @OA\Response(response="404", description="Note not found."),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function index(Request $request)
    {
        $note = Note::find($request->note);
        if (empty($note)) {
            return response()->json([
                'message' => 'Note not found'
            ], 404);
        }

        if ($request->user()->cannot('view', $note)) {
            return response()->json([
                'message' => 'You do not own this note.'
            ], 403);
        }
        $noteTags = NoteTag::where('note_id', $request->note)->paginate(5);
        return new NoteTagCollection($noteTags);
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * @OA\Post(
     *     path="/api/notes/{note}/tags",
     *     summary="Add a tag to the specified note",
     *     @OA\Parameter(name="note", in="path", required=true, description="ID of the note", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Note tag created successfully", @OA\JsonContent(ref="#/components/schemas/NoteTagResource")),
     *     @OA\Response(response="403", description="You do not own this note."),
     *     @OA\Response(response="404", description="Note not found."),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function store(StoreNoteTagRequest $request)
    {
        $validated = $request->validated();
        $note = Note::find($request->note);

        if (empty($note)) {
            return response()->json([
                'message' => 'Note not found'
            ], 404);
        }

        if ($request->user()->cannot('update', $note)) {
            return response()->json([
                'message' => 'You do not own this note.'
            ], 403);
        }

        $tag = Tag::create([
            'name' => $validated['name']
        ]);

        $noteTag = NoteTag::create([
            'tag_id' => $tag->id,
            'note_id' => $request->note,
        ]);

        return new NoteTagResource($noteTag);
    }

    /**
     * Display the specified resource.
     */

    /**
     * @OA\Get(
     *     path="/api/notes/{note}/tags/{tag}",
     *     summary="Display the specified note tag",
     *     @OA\Parameter(name="note", in="path", required=true, description="ID of the note", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="tag", in="path", required=true, description="ID of the tag", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Note tag retrieved successfully", @OA\JsonContent(ref="#/components/schemas/NoteTagResource")),
     *     @OA\Response(response="403", description="You do not own this note."),
     *     @OA\Response(response="404", description="Note tag or note not found."),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function show(Request $request)
    {
        $note = Note::find($request->note);
        if (empty($note)) {
            return response()->json([
                'message' => 'Note not found'
            ], 404);
        }

        if ($request->user()->cannot('view', $note)) {
            return response()->json([
                'message' => 'You do not own this note.'
            ], 403);
        }

        $noteTag = NoteTag::find($request->tag);
        if (empty($noteTag)) {
            return response()->json([
                'message' => 'Tag not found'
            ], 404);
        }
        return new NoteTagResource($noteTag);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/notes/{note}/tags/{tag}",
     *     summary="Remove the specified note tag",
     *     @OA\Parameter(name="note", in="path", required=true, description="ID of the note", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="tag", in="path", required=true, description="ID of the tag", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Note tag deleted successfully"),
     *     @OA\Response(response="403", description="You do not own this note."),
     *     @OA\Response(response="404", description="Note tag or note not found."),
     *     security={{"bearerAuth": {}}}
     * )
     */

    public function destroy(Request $request)
    {
        $note = Note::find($request->note);
        if (empty($note)) {
            return response()->json([
                'message' => 'Note not found'
            ], 404);
        }

        if ($request->user()->cannot('delete', $note)) {
            return response()->json([
                'message' => 'You do not own this note.'
            ], 403);
        }

        $noteTag = NoteTag::find($request->tag);
        if (!empty($noteTag)) {
            $tag = $noteTag->tag();
            $noteTag->delete();
            $tag->delete();
            return response()->json([
                'message' => 'Tag deleted'
            ], 200);
        }
        return response()->json([
            'message' => 'Tag not found'
        ], 404);
    }
}
