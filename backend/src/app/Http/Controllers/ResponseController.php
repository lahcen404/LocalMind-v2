<?php

namespace App\Http\Controllers;

use App\Http\Resources\ResponseResource;
use App\Models\Question;
use App\Models\Response as UserResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class ResponseController extends Controller
{
   /**
    * @OA\Post(
    *     path="/api/questions/{question}/responses",
    *     tags={"Responses"},
    *     summary="Create a response for a question",
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
    *         @OA\JsonContent(ref="#/components/schemas/ResponseRequest")
    *     ),
    *     @OA\Response(
    *         response=201,
    *         description="Response created successfully",
    *         @OA\JsonContent(ref="#/components/schemas/ResponseMutationResponse")
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
    /**
     * @OA\Put(
     *     path="/api/responses/{response}",
     *     tags={"Responses"},
     *     summary="Update a response",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="response",
     *         in="path",
     *         required=true,
     *         description="Response id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ResponseRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Response updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ResponseMutationResponse")
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
    /**
     * @OA\Delete(
     *     path="/api/responses/{response}",
     *     tags={"Responses"},
     *     summary="Delete a response",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="response",
     *         in="path",
     *         required=true,
     *         description="Response id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Response deleted successfully",
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
    public function destroy(UserResponse $response)
    {
        if (Auth::id() !== $response->user_id) {
            return response()->json(['message' => 'You can only delete your own response.'], 403);
        }
        $response->delete();
        return response()->json(['message' => 'Response deleted successfully!!']);
    }
}
