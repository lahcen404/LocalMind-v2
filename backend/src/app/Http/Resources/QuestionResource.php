<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'location' => $this->location,

            'created_at' => $this->created_at->diffForHumans(),
            'timestamp' => $this->created_at->format('d M Y H:i'),


            'author' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'initial' => strtoupper(substr($this->user->name, 0, 1)),
            ],

            'metrics' => [
                'responses_count' => (int) ($this->responses_count ?? $this->responses()->count()),
                'favorites_count' => (int) ($this->favorited_by_count ?? $this->favoritedBy()->count()),
            ],
            'responses' => ResponseResource::collection($this->whenLoaded('responses')),

        ];
    }
}
