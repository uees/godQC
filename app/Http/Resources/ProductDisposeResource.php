<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProductDisposeResource extends Resource
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
            'product_batch_id' => (int) $this->product_batch_id,
            'from_record_id' => (int) $this->from_record_id,
            'to_record_id' => (int) $this->to_record_id,
            'method' => $this->method,
            'author' => $this->author,
            'memo' => $this->memo,
            'productBatch' => ProductBatchResource::make($this->whenLoaded('productBatch')),
            'recordFrom' => TestRecordResource::make($this->whenLoaded('recordFrom')),
            'recordTo' => TestRecordResource::make($this->whenLoaded('recordTo')),
        ];
    }
}
