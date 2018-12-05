<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_name',
        'product_name_suffix',
        'batch_number',
        'type',
        'amount',
        'memo',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testRecords()
    {
        return $this->hasMany(TestRecord::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disposes()
    {
        return $this->hasMany(ProductDispose::class);
    }
}
