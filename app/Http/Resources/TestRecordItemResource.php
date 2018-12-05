<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TestRecordItemResource extends Resource
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
            'test_record_id' => (int) $this->test_record_id,
            'item' => $this->item,
            'spec' => $this->spec,
            'value' => $this->value,
            'fake_value' => $this->fake_value,
            'conclusion' => $this->conclusion,
            'tester' => $this->tester,
            'memo' => $this->memo,
            'testRecord' => TestRecordResource::make($this->whenLoaded('testRecord'))
        ];
    }
}
