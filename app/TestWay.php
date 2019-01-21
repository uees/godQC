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
            'name' => '',
            'method' => '',
            'method_id' => '',
            'spec' => [
                'is_show' => true,   // 是否展示
                'required' => true,  // 是否必须填值项
                'value_type' => '',  // RANGE, INFO, NUMBER, ONLY_SHOW
                'data' => [
                    'min' => 0,
                    'max' => 0,
                    'value' => '',
                ],
            ],
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'test_way_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany(Category::class, 'test_way_id');
    }
}
