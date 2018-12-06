<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestWay extends Model
{
    protected $fillable = [
        'name',
        'way',
    ];

    protected $casts = [
        'way' => 'array',
    ];

    // item of the way array
    public function newItem()
    {
        return [
            'name'=> '',
            'method' => '',
            'method_id' => '',
            'spec' => [
                'value_type' => '',  // RANGE, INFO, NUMBER
                'data' => [
                    'min' => 0,
                    'max' => 0,
                    'value' => '',
                ],
            ],
        ];
    }
}
