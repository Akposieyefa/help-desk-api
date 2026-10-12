<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncidentResource extends JsonResource
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
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'data' => $this->data,
            'user_id' => $this->user_id,
            'acting_officer' => $this->acting_officer,
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
