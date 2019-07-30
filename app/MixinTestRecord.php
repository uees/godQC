<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class MixinTestRecord extends Model
{
    protected $fillable = [
        'product_id',  // is part a batch id
        'part_a_name',
        'part_a_batch',
        'part_b_name',
        'part_b_batch',
        'conclusion',
        'testers',
        'is_archived',
        'show_reality',
        'memo',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'completed_at',
    ];

    protected $casts = [
        'show_reality' => 'boolean',
        'is_archived' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(MixinTestRecordItem::class, 'mixin_test_record_id')
            ->orderBy('id');  // 排序
    }
}
