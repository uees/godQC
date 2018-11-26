<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array(
            'id' => (int)$this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'metas' => $this->metas,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
        );
    }
}
