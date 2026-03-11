<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class FavoriteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/favorites",
     *     tags={"Favorites"},
     *     summary="List the authenticated user's favorite questions",
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
     *         description="Paginated favorite questions",
     *         @OA\JsonContent(ref="#/components/schemas/QuestionPaginatedResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
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
    /**
     * @OA\Post(
     *     path="/api/questions/{question}/favorite",
     *     tags={"Favorites"},
     *     summary="Toggle favorite status for a question",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="question",
     *         in="path",
     *         required=true,
     *         description="Question id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favorite status toggled",
     *         @OA\JsonContent(ref="#/components/schemas/FavoriteToggleResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
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
