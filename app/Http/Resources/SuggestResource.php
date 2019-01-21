<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SuggestResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int)$this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'parent_id' => (int)$this->parent_id,
            'name' => $this->name,
            'data' => $this->data,
            'memo' => $this->memo,
            'parent' => SuggestResource::make($this->whenLoaded('parent')),
            'children' => SuggestResource::collection($this->whenLoaded('children')),
        ];
    }
}
