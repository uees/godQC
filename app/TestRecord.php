<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestRecord extends Model
{
    protected $fillable = [
        'product_batch_id',
        'show_reality',
        'test_times',
        'conclusion',
        'testers',
        'is_archived',
        'memo',
    ];

    /**
     * 应被转换为日期的属性。
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'completed_at',
        'said_package_at',
    ];

    protected $casts = [
        'show_reality' => 'boolean',
        'is_archived' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function batch()
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(TestRecordItem::class, 'test_record_id')
            ->orderBy('id');  // 排序
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
