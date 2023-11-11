<?php

namespace App\Http\Controllers;


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
    public function store(Request $request)
    {
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
            'name' => $request->name
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
        $tag = $noteTag->tag();

        $noteTag->delete();
        $tag->delete();
        return response()->json([
            'message' => 'Tag deleted'
        ], 200);
    }
}
