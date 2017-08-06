<?php

namespace App\Models;

use Eloquent;

/**
 * @property int extend
 */
class LoanProductExtend extends Eloquent
{
    public $timestamps = false;

    protected $fillable = [
        'extend'
    ];

    protected $appends = [
        'label'
    ];

    /**
     * 输出对应文本
     * @return string
     */
    public function getLabelAttribute()
    {
        static $labels = null;
        if (empty($labels)) {
            $labels = array_column(config('loan.extend'), 'label', 'value');
        }

        return isset($labels[$this->extend]) ? $labels[$this->extend] : '';
    }


}