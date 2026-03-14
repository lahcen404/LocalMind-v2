<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Question;
use App\Models\Response as ResponseModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Dashboard stats (admin only).
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        if (Auth::user()->role !== UserRole::ADMIN) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json([
            'users_count' => User::count(),
            'questions_count' => Question::count(),
            'responses_count' => ResponseModel::count(),
        ]);
    }
}
