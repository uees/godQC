<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TestRecordResource extends Resource
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
            'show_reality' => (boolean)$this->show_reality,
            'product_batch_id' => (int)$this->product_batch_id,
            'test_times' => (int)$this->test_times,
            'conclusion' => $this->conclusion,
            'testers' => $this->testers,
            'completed_at' => $this->completed_at,
            'said_package_at' => $this->said_package_at,
            'memo' => $this->memo,
            'batch' => ProductBatchResource::make($this->whenLoaded('batch')),
            'items' => TestRecordItemResource::collection($this->whenLoaded('items')),
            'willDispose' => ProductDisposeResource::make($this->whenLoaded('willDispose')),
            'disposed' => ProductDisposeResource::make($this->whenLoaded('disposed')),
        ];
    }
}
