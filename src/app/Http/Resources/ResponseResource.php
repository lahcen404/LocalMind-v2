<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseResource extends JsonResource
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
            'content' => $this->content,
            'question_id' => $this->question_id,

            'created_at' => $this->created_at->diffForHumans(),


            'author' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'initial' => strtoupper(substr($this->user->name, 0, 1)),
            ],


            'is_owner' => auth('sanctum')->id() === $this->user_id,
        ];
    }
}
