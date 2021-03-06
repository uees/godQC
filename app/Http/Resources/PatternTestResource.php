<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PatternTestResource extends Resource
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
            'batch_number' => $this->batch_number,
            'nai_han_xing' => $this->nai_han_xing,
            'nai_rong_ji' => $this->nai_rong_ji,
            'nai_suan_jian' => $this->nai_suan_jian,
            'h12_xian_ying' => $this->h12_xian_ying,
            'h24_xian_ying' => $this->h24_xian_ying,
            'ge_ye_xian_ying' => $this->ge_ye_xian_ying,
            'ge_ye_bao_guang' => $this->ge_ye_bao_guang,
            'die_ban' => $this->die_ban,
            'lao_hua' => $this->lao_hua,
            'tester' => $this->tester,
        ];
    }
}
