<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestRecordItem extends Model
{
    protected $fillable = [
        'test_record_id',
        'item',
        'spec',
        'value',
        'conclusion',
        'tester',
        'memo',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function test_record()
    {
        return $this->belongsTo(TestRecord::class);
    }
}
