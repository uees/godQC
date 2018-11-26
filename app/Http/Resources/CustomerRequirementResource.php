<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CustomerRequirementResource extends Resource
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
            'customer_id' => (int)$this->customer_id,
            'product' => $this->product,
            'item' => $this->item,
            'requirement' => $this->requirement,
            'value_type' => $this->value_type,
            'value' => $this->value,
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
        ];
    }
}
