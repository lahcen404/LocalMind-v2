<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Http\Resources\QuestionResource;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();


        $user->loadCount(['questions', 'responses', 'favoriteQuestions']);

        return new ProfileResource($user);
    }

    public function myQuestions()
    {
        $questions = Auth::user()->questions()
            ->withCount(['responses', 'favoritedBy'])
            ->latest()
            ->paginate(10);

        return QuestionResource::collection($questions);
    }
}
