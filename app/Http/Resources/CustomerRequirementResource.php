<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CustomerRequirementResource extends Resource
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
            'customer_id' => (int)$this->customer_id,
            'product_id' => (int)$this->product_id,
            'item' => $this->item,
            'spec' => $this->spec,
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'product' => ProductResource::make($this->whenLoaded('product')),
        ];
    }
}
