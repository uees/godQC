<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PatternTest extends Model
{
    protected $fillable = [
        'product_name',
        'batch_number',
        'nai_han_xing',
        'nai_rong_ji',
        'nai_suan_jian',
        '12h_xian_ying',
        '24h_xian_ying',
        'ge_ye_xian_ying',
        'ge_ye_bao_guang',
        'die_ban',
        'lao_hua',
        'tester',
    ];
}
