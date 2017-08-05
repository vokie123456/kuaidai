<?php

namespace App\Models;

use Eloquent;

/**
 * @property int id
 */
class LoanInfoForms extends Eloquent
{

    protected $fillable = [
        'user_id', 'name', 'id_card', 'loan_amount', 'loan_deadline', 'loan_deadline_type', 'use_loan_time', 'job'
    ];

    /**
     * 关联更多信息
     * extends为系统关键字，所以改为_extends
     */
    public function _extends()
    {
        return $this->hasMany(LoanInfoExtends::class, 'form_id', 'id');
    }


}