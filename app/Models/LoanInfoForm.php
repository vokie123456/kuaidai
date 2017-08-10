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
 * @property int loan_deadline_day
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

    /**
     * 关联用户
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}