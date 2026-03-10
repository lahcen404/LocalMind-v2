<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class QuestionController extends Controller
{
   public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $location = $request->input('location');

        $questions = Question::with(['user'])
            ->withCount(['responses', 'favoritedBy'])
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                      ->orWhere('content', 'like', "%{$keyword}%");
                });
            })
            ->when($location, function ($query, $location) {
                return $query->where('location', 'like', "%{$location}%");
            })
            ->latest()
            ->paginate(15);


        return QuestionResource::collection($questions);
    }

    // create a question
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'location' => 'required|string',
        ]);

        $question = Auth::user()->questions()->create($validated);

        return response()->json([
            'message' => 'Question created successfully!',
            'data' => new QuestionResource($question)
        ], 201);
    }

    // show a single question
    public function show(Question $question)
    {
        $question->load(['user', 'responses.user']);
        return new QuestionResource($question);
    }

    // update a question
    public function update(Request $request, Question $question)
    {
        if (Auth::id() !== $question->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'location' => 'required|string',
        ]);

        $question->update($validated);

        return response()->json([
            'message' => 'Question updated successfully!!',
            'data' => new QuestionResource($question)
        ]);
    }

    // delete a question
    public function destroy(Question $question)
    {
        if (Auth::id() === $question->user_id || Auth::user()->role === UserRole::ADMIN) {
            $question->delete();
            return response()->json(['message' => 'Question deleted successfully!!']);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }


}
