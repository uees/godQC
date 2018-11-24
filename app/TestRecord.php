<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestRecord extends Model
{
    protected $fillable = [
        'product_batch_id',
        'test_times',
        'conclusion',
        'testers',
        'memo',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function batch()
    {
        return $this->belongsTo(ProductBatch::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(TestRecordItem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function willDispose()
    {
        return $this->hasOne(ProductDispose::class, 'from_record_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function disposed()
    {
        return $this->hasOne(ProductDispose::class, 'to_record_id');
    }
}
