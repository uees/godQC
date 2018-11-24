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

    // the way is about the steps array
    public function newStep()
    {
        return [
            'name'=> '',
            'method' => '',
            'method_id' => '',
            'spec' => '',
            'value_type' => '',
            'value' => ''
        ];
    }
}
