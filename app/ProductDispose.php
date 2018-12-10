<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductDispose extends Model
{
    protected $fillable = [
        'product_batch_id',
        'from_record_id',
        'to_record_id',
        'method',
        'author',
        'memo',
        'is_done',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function recordFrom()
    {
        return $this->belongsTo(TestRecord::class, 'from_record_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function recordTo()
    {
        return $this->belongsTo(TestRecord::class, 'to_record_id');
    }
}
