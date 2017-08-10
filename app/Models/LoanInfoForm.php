<?php

namespace App\Models;

use Eloquent;

/**
 * @property int id
 * @property float loan_amount
 * @property string loan_deadline_type
 * @property int loan_deadline
 * @property string use_loan_time
 * @property int job
 * @property LoanInfoExtend[]|\Illuminate\Database\Eloquent\Collection _extends
 */
class LoanInfoForm extends Eloquent
{

    protected $fillable = [
        'user_id', 'name', 'id_card', 'loan_amount', 'loan_deadline', 'loan_deadline_type', 'loan_deadline_day', 'use_loan_time', 'job'
    ];

    /**
     * 关联更多信息
     * extends为系统关键字，所以改为_extends
     */
    public function _extends()
    {
        return $this->hasMany(LoanInfoExtend::class, 'form_id', 'id');
    }


}