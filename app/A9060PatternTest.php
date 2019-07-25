<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class A9060PatternTest extends Model
{
    protected $table = 'a9060_pattern_tests';

    protected $fillable = [
        'product_name',
        'batch_number',
        'ge_ye_xian_ying',
        'ge_ye_bao_guang',
        'die_ban',
        'lao_hua',
        'tester',
    ];
}
