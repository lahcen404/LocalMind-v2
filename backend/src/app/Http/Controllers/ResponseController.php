<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\ResponseResource;
use App\Models\Question;
use App\Models\Response as UserResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResponseController extends Controller
{
   public function store(Request $request, Question $question)
    {
        $validated = $request->validate([
            'content' => 'required|min:5|max:2000',
        ]);

        // creaate a response for the question
        $response = $question->responses()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Intelligence contribution recorded.',
            'data' => new ResponseResource($response)
        ], 201);
    }

    //update a response
    public function update(Request $request, UserResponse $response)
    {
        if (Auth::id() !== $response->user_id) {
            return response()->json(['message' => 'You cannot update !!'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|min:5|max:2000',
        ]);

        $response->update($validated);

        return response()->json([
            'message' => 'Intelligence updated.',
            'data' => new ResponseResource($response)
        ]);
    }

    // delete a response
    public function destroy(UserResponse $response)
    {
        // only author or admin can delete
        if (Auth::id() === $response->user_id || Auth::user()->role === UserRole::ADMIN) {
            $response->delete();
            return response()->json(['message' => 'Response deleted successfully!!']);
        }

        return response()->json(['message' => 'Unauthorized!!'], 403);
    }
}
