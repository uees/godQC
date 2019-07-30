<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;


class MixinTestRecordResource extends Resource
{
    public function toArray($request)
    {
        $attributes = $this->attributesToArray();
        $attributes['product'] = ProductResource::make($this->whenLoaded('product'));
        $attributes['items'] = MixinTestRecordItemResource::collection($this->whenLoaded('items'));
        return $attributes;
    }
}
