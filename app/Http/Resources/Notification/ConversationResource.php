<?php

namespace App\Http\Resources\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->is_group ? $this->name : $this->users->first()->name,
            'is_group' => $this->is_group,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
