<?php

namespace App\Models;

use Eloquent;

class LoanInfoExtends extends Eloquent
{

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'extend'
    ];


}