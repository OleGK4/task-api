<?php

namespace App\Http\Controllers;

use App\Http\Resources\NoteCollection;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;
use Psy\Util\Json;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userNotes = $request->user()->notes()->paginate(5);

        if ($userNotes->count() > 0) {
            return new NoteCollection($userNotes);
        } else {
            return response()->json([
                'message' => 'No notes found.'
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $note = Note::create([
            'title' => $request->title,
            'text' => $request->text,
            'user_id' => $request->user()->id,
        ]);
        return response()->json([
            'note' => $note
        ], 201);
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
        return new NoteResource($note);
    }


    public function showByTag(Request $request)
    {
        // Not done yet!

//        $tags = $request->search_tag;
//
//        // Convert the comma-separated tags into an array
//        $tagsArray = explode('$', $tags);
//
//
//        dump($tagsArray);
//        dd($tags);
//
//
//        $note = Note::find($request->note);
//        if (empty($note)) {
//            return response()->json([
//                'message' => 'Note not found'
//            ], 404);
//        }
//
//        if ($request->user()->cannot('view', $note)) {
//            return response()->json([
//                'message' => 'You do not own this note.'
//            ], 403);
//        }
//        return new NoteResource($note);
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
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

        if ($request->title) {
            $note->update([
                'title' => $request->title,
            ]);
        }
        if ($request->text) {
            $note->update([
                'text' => $request->text,
            ]);
        }

        return response()->json([
            new NoteResource($note)
        ]);
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

        $note->delete();
        return response()->json([
            'message' => 'Note deleted'
        ], 200);
    }
}
