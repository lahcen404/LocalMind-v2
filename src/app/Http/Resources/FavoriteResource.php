<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'favorite_id' => $this->pivot ? $this->pivot->id : null,

            'saved_at' => $this->pivot ? $this->pivot->created_at->diffForHumans() : null,

            // get questioon data
            'question' => new QuestionResource($this),


            'status' => 'archived_in_vault'
        ];
    }
}
