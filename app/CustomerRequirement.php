<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerRequirement extends Model
{
    protected $fillable = [
        'customer_id',
        'product_id',
        'item',
        'spec',
    ];

    protected $casts = [
        'spec' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
