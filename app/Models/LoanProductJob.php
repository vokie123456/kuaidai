<?php

namespace App\Models;

use Eloquent;

class LoanProductJob extends Eloquent
{

    public $timestamps = false;

    protected $fillable = [
        'job'
    ];


}