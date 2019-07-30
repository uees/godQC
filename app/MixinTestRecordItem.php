<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class MixinTestRecordItem extends Model
{
    protected $fillable = [
        'mixin_test_record_id',
        'item',
        'spec',
        'value',
        'fake_value',
        'conclusion',
        'tester',
        'memo',
    ];

    protected $casts = [
        'spec' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mixinTestRecord()
    {
        return $this->belongsTo(MixinTestRecord::class, 'mixin_test_record_id');
    }
}
