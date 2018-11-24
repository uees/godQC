<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use MetaTrait;

    protected $fillable = [
        'internal_name',
        'market_name',
        'part_a',
        'part_b',
        'ab_ratio',
        'color',
        'spec',
        'label_viscosity',
        'viscosity_width',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_product', 'product_id', 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function testWays()
    {
        return $this->morphToMany(TestWay::class, 'testable');
    }
}
