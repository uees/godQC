<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class DisqualificationStatisticsResource extends Resource
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
            'year' => (int)$this->year,
            'month' => (int)$this->month,
            'qc_type' => $this->qc_type,
            'category_id' => (int)$this->category_id,
            'item' => $this->item,
            'amount' => (int)$this->amount,
            'rate' => (float)$this->rate,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => CategoryResource::make($this->whenLoaded('category')),
        ];
    }
}
