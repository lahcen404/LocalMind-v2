<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // registeer
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'device_name' => 'required'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::MEMBER
        ]);

        // generate tokeen
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'message' => 'Identity successfully registered in the network.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value
            ]
        ], 201);
    }

    // login
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
    public function logout(Request $request)
    {
        // delete tokeen for current device
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Tokeen deleted successfully!!'
        ]);
    }
}
