<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->value ?? $this->role,
            'initial' => strtoupper(substr($this->name, 0, 1)),
            'joined_at' => $this->created_at->format('M Y'),

            'stats' => [
                'questions_count' => $this->questions_count ?? $this->questions()->count(),
                'responses_count' => $this->responses_count ?? $this->responses()->count(),
                'favorites_count' => $this->favorite_questions_count ?? $this->favoriteQuestions()->count(),
            ],

            'recent_questions' => QuestionResource::collection($this->whenLoaded('questions')),
        ];
    }
}
