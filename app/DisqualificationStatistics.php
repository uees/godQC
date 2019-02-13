<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DisqualificationStatistics extends Model
{
    protected $table = 'disqualification_statistics';

    protected $fillable = [
        'year',
        'month',
        'qc_type',
        'item',
        'amount',
        'rate'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
