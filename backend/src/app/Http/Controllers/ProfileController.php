<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Http\Resources\QuestionResource;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/profile",
     *     tags={"Profile"},
     *     summary="Get the authenticated user's profile",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated profile data",
     *         @OA\JsonContent(ref="#/components/schemas/ProfileResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function show()
    {
        $user = Auth::user();
        $user->loadCount(['questions', 'responses', 'favoriteQuestions']);
        $user->setRelation(
            'questions',
            $user->questions()->with('user')->withCount(['responses', 'favoritedBy'])->latest()->take(10)->get()
        );

        return new ProfileResource($user);
    }

    /**
     * @OA\Get(
     *     path="/api/profile/questions",
     *     tags={"Profile"},
     *     summary="List questions created by the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Pagination page number",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated user question list",
     *         @OA\JsonContent(ref="#/components/schemas/QuestionPaginatedResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function myQuestions()
    {
        $questions = Auth::user()->questions()
            ->withCount(['responses', 'favoritedBy'])
            ->latest()
            ->paginate(10);

        return QuestionResource::collection($questions);
    }
}
