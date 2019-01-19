<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use MetaTrait;

    protected $fillable = [
        'category_id',
        'test_way_id',
        'internal_name',
        'market_name',
        'part_a',
        'part_b',
        'ab_ratio',
        'color',
        'spec',
        'label_viscosity',
        'viscosity_width',
        'metas',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_product', 'product_id', 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testWay()
    {
        return $this->belongsTo(TestWay::class);
    }
}
