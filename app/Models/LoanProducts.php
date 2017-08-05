<?php

namespace App\Models;

use DB;
use Eloquent;

/**
 * @property LoanProductJob|\Illuminate\Database\Eloquent\Collection jobs
 * @property LoanProductExtend|\Illuminate\Database\Eloquent\Collection _extends
 */
class LoanProducts extends Eloquent
{

    /** 状态-启用 */
    const STATUS_ENABLED = 1;

    /**
     * 关联职业信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobs()
    {
        return $this->hasMany(LoanProductJob::class, 'product_id', 'id');
    }

    /**
     * 关联补充信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function _extends()
    {
        return $this->hasMany(LoanProductExtend::class, 'product_id', 'id');
    }

    /**
     * 筛选出符合用户的贷款产品
     * @param LoanInfoForm $form
     * @return array
     */
    public function getUserCases(LoanInfoForm $form)
    {
        // 距离用款剩余秒数
        $time = strtotime($form->use_loan_time) - time();

        // 减掉3天并换算成小时
        $hour = ($time - 3 * 24 * 3600) / 3600;

        $fields = ['id', 'name', 'logo', 'loan_limit_min', 'loan_limit_max', 'deadline_min', 'deadline_max', 'deadline_type', 'loaneders'];
        /** @var static[] $rows */
        $rows = $this->select($fields)
            ->with('jobs', '_extends')

            ->where('status', self::STATUS_ENABLED)

            // 代款金额
            ->where('loan_limit_min', '<=', $form->loan_amount)
            ->where('loan_limit_max', '>=', $form->loan_amount)

            // 代款期限
            ->where('deadline_type', $form->loan_deadline_type)
            ->where('deadline_min', '<=', $form->loan_deadline)
            ->where('deadline_max', '>=', $form->loan_deadline)

            // 算法：
            // 用款日期 - 当前时间 = 剩余时间；
            // (审核+放款)时间 > 剩余时间 - 3天；
            ->whereRaw(DB::raw("audit_cycle + loan_time >= {$hour}"))

            // 按推荐-时间排序
            ->orderByRaw('field(recommend, 1) desc, id desc')
            ->get();

        // 从结果集中筛选出符合职业信息、补充信息的记录
        $cases = [];
        foreach ($rows as $index => $row) {
            // 最多返回4条
            if (count($cases) >= 4) {
                break;
            }

            // 职业信息
            $jobs = array_column($row->jobs->toArray(), 'job');
            if (!empty($jobs) && !in_array($form->job, $jobs)) {
                continue;
            }

            // 补充信息
            $extends = array_column($row->_extends->toArray(), 'extend');
            $extendFlag = false;
            foreach ($form->_extends as $extend) {
                if (empty($extends) || in_array($extend->extend, $extends)) {
                    $extendFlag = true;
                    break;
                }
            }
            if (!$extendFlag) {
                continue;
            }

            $cases[] = $row->toArray();
        }

        return $cases;
    }
}