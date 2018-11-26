<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TestWayResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int)$this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'name' => $this->name,
            'way' => $this->way,
        ];
    }
}
