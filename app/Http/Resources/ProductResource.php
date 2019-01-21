<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProductResource extends Resource
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
            'category_id' => (int)$this->category_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'internal_name' => $this->internal_name,
            'market_name' => $this->market_name,
            'part_a' => $this->part_a,
            'part_b' => $this->part_b,
            'ab_ratio' => $this->ab_ratio,
            'color' => $this->color,
            'spec' => $this->spec,
            'label_viscosity' => $this->label_viscosity,
            'viscosity_width' => $this->viscosity_width,
            'metas' => $this->metas,
            'customers' => CustomerResource::collection($this->whenLoaded('customers')),
            'testWay' => TestWayResource::make($this->whenLoaded('testWay')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
        ];
    }
}
