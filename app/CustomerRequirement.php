<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerRequirement extends Model
{
    protected $fillable = [
        'customer_id',
        'product',
        'item',
        'requirement',
        'value_type',
        'value',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
