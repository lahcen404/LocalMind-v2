<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
       public function index()
    {
        $questions = Auth::user()
            ->favoriteQuestions()
            ->with(['user'])
            ->withCount(['responses', 'favoritedBy'])
            ->latest()
            ->paginate(15);

        return QuestionResource::collection($questions);
    }
     public function toggle(Question $question)
    {
        // toggle for like & unlike btn
        $status = Auth::user()->favoriteQuestions()->toggle($question->id);

        $isAttached = count($status['attached']) > 0;

        return response()->json([
            'message' => $isAttached ? 'Question added to favorites !' : 'Question removed from favorites !',
            'is_favorited' => $isAttached,
            'favorites_count' => $question->favoritedBy()->count()
        ]);
    }
}
