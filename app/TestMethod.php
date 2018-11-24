<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestMethod extends Model
{
    protected $fillable = [
        'name',
        'file',
        'content',
    ];
}
