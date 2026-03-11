<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;
class QuestionController extends Controller
{
   /**
    * @OA\Get(
    *     path="/api/questions",
    *     tags={"Questions"},
    *     summary="List questions",
    *     @OA\Parameter(
    *         name="keyword",
    *         in="query",
    *         required=false,
    *         description="Filter questions by keyword",
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Parameter(
    *         name="location",
    *         in="query",
    *         required=false,
    *         description="Filter questions by location",
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Parameter(
    *         name="page",
    *         in="query",
    *         required=false,
    *         description="Pagination page number",
    *         @OA\Schema(type="integer", minimum=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Paginated question list",
    *         @OA\JsonContent(ref="#/components/schemas/QuestionPaginatedResponse")
    *     )
    * )
    */
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
    /**
     * @OA\Post(
     *     path="/api/questions",
     *     tags={"Questions"},
     *     summary="Create a new question",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/QuestionRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Question created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/QuestionMutationResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
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
    /**
     * @OA\Get(
     *     path="/api/questions/{question}",
     *     tags={"Questions"},
     *     summary="Show a question with its responses",
     *     @OA\Parameter(
     *         name="question",
     *         in="path",
     *         required=true,
     *         description="Question id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Question details",
     *         @OA\JsonContent(ref="#/components/schemas/QuestionResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Question not found"
     *     )
     * )
     */
    public function show(Question $question)
    {
        $question->load(['user', 'responses.user']);
        return new QuestionResource($question);
    }

    // update a question
    /**
     * @OA\Put(
     *     path="/api/questions/{question}",
     *     tags={"Questions"},
     *     summary="Update a question",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="question",
     *         in="path",
     *         required=true,
     *         description="Question id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/QuestionRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Question updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/QuestionMutationResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
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
    /**
     * @OA\Delete(
     *     path="/api/questions/{question}",
     *     tags={"Questions"},
     *     summary="Delete a question",
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
     *         description="Question deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
     *     )
     * )
     */
    public function destroy(Question $question)
    {
        if (Auth::id() === $question->user_id || Auth::user()->role === UserRole::ADMIN) {
            $question->delete();
            return response()->json(['message' => 'Question deleted successfully!!']);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }


}
