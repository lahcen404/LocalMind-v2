<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    // registeer
   /**
    * @OA\Post(
    *     path="/api/register",
    *     tags={"Auth"},
    *     summary="Register a new user",
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
    *     ),
    *     @OA\Response(
    *         response=201,
    *         description="User registered successfully",
    *         @OA\JsonContent(ref="#/components/schemas/RegisterSuccessResponse")
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
    *     )
    * )
    */
   public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'device_name' => 'required',
            'role' => 'nullable|string'
        ]);


        $roleToAssign = UserRole::MEMBER;

        if ($request->has('role')) {

            $requestedRole = UserRole::tryFrom(strtolower($request->role));
            if ($requestedRole) {
                $roleToAssign = $requestedRole;
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $roleToAssign
        ]);

        $roleValue = $user->role instanceof UserRole ? $user->role->value : UserRole::MEMBER->value;
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully!!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $roleValue
            ]
        ], 201);
    }

    // login
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Log in a user and create a Sanctum token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AuthSuccessResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Your credentials are incorrect!!'
            ], 401);
        }

        // generate new tokeen
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value
            ]
        ]);
    }

    // logout
    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="Log out the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        // delete tokeen for current device
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Tokeen deleted successfully!!'
        ]);
    }
}
