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

    /*
      'spec' => [
            'is_show' => true,   // 是否展示
            'required' => true,  // 是否必须填值项
            'value_type' => '',  // RANGE, INFO, NUMBER, ONLY_SHOW
            'data' => [
                'min' => 0,
                'max' => 0,
                'value' => '',
                'memo' => '',
                'unit' => '',
            ],
        ],
    */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testRecord()
    {
        return $this->belongsTo(TestRecord::class, 'test_record_id');
    }
}
