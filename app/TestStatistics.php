<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestStatistics extends Model
{
    protected $table = 'test_statistics';

    protected $fillable = [
        'year',
        'month',
        'qc_type',
        'tests_num',
        'once_disqualification_num',
        'disqualification_num',
        'force_accept_num',
        // 'category_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
