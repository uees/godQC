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
    public function testRecord()
    {
        return $this->belongsTo(TestRecord::class);
    }
}
