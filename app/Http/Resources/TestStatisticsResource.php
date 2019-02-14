<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TestStatisticsResource extends Resource
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
            'tests_num' => (int)$this->tests_num,
            'once_disqualification_num' => (int)$this->once_disqualification_num,
            'disqualification_num' => (int)$this->disqualification_num,
            'force_accept_num' => (int)$this->force_accept_num,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => CategoryResource::make($this->whenLoaded('category')),
        ];
    }
}
