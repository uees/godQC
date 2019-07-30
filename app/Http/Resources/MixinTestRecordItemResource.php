<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;


class MixinTestRecordItemResource extends Resource
{
    public function toArray($request)
    {
        $attributes = $this->attributesToArray();
        $attributes['mixinTestRecord'] = MixinTestRecordResource::make($this->whenLoaded('mixinTestRecord'));
        return $attributes;
    }
}