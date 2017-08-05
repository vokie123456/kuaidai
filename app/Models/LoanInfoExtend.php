<?php

namespace App\Models;

use Eloquent;

/**
 * @property int extend
 */
class LoanInfoExtend extends Eloquent
{

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'extend'
    ];


}