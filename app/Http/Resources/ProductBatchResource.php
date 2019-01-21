<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProductBatchResource extends Resource
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
            'product_name' => $this->product_name,
            'product_name_suffix' => $this->product_name_suffix,
            'batch_number' => $this->batch_number,
            'type' => $this->type,
            'amount' => $this->amount,
            'tests_num' => $this->tests_num,
            'memo' => $this->memo,
            'testRecords' => TestMethodResource::collection($this->whenLoaded('testRecords')),
            'disposes' => ProductDisposeResource::collection($this->whenLoaded('disposes')),
        ];
    }
}
