<?php

namespace App\Http\Controllers\Admin\Loan;

use App\Http\Controllers\Controller;
use App\Models\LoanInfoForm;
use Illuminate\Http\Request;
use PHPExcel;
use PHPExcel_IOFactory;

/**
 * 贷款信息管理
 * @menu index 贷款信息管理
 * @nodeTitle 贷款信息管理
 * @nodeName index 列表
 * @nodeName export 导出
 */
class FormController extends Controller
{

    private $searchFields = ['area', 'updated_at'];

    /**
     * 贷款信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = LoanInfoForm::with('user', '_extends')
            ->orderBy('id', 'desc');

        $this->buildSearch($query, $request);

        $data = array(
            'paginate' => $query->paginate(),
            'baseData' => config('loan'),
            'search' => $request->input('search')
        );

        return view('admin.loan.form.index', $data);
    }

    /**
     * 导出
     * @param Request $request
     */
    public function export(Request $request)
    {
        $query = LoanInfoForm::with('user', '_extends')
            ->orderBy('id', 'desc');

        $this->buildSearch($query, $request);

        $rows = $query->get()
            ->toArray();

        $fields = array(
            'user.username' => '手机',
            'name' => '姓名',
            'id_card' => '身份证',
            'loan_amount' => '借款金额',
            'formatLoanAmount' => '借款期限',
            'use_loan_time' => '用款时间',
            'job' => '职业信息',
            'area' => '地区',
            '_extends' => '补充信息',
            'updated_at' => '日期',
        );

        $loanConfig = config('loan');
        $jobsHash = array_column($loanConfig['jobs'], 'label', 'value');
        $extendHash = array_column($loanConfig['extend'], 'label', 'value');

        $formatter = array(
            'formatLoanAmount' => function($row) {return $row['loan_deadline'].$row['loan_deadline_type'];},
            'job' => function($row) use ($jobsHash) {return isset($jobsHash[$row['job']]) ? $jobsHash[$row['job']] : '未知';},
            '_extends' => function($row) use ($extendHash) {
                $extends = [];
                foreach($row['_extends'] as $extend) {
                    if (isset($extendHash[$extend['extend']])) {
                        $extends[] = $extendHash[$extend['extend']];
                    }
                }

                return implode('、', $extends);
            },
        );

        $objPHPExcel = new PHPExcel();
        $Sheet = $objPHPExcel->setActiveSheetIndex(0);

        // 设置表头
        $chr = 65;
        foreach ($fields as $label) {
            $Sheet->getCell(chr($chr) . '1')->setValueExplicit($label);
            $chr++;
        }

        $num = 2;
        foreach ($rows as $row) {
            $chr = 65;
            foreach ($fields as $field => $label) {
                if (isset($formatter[$field])) {
                    $value = $formatter[$field]($row);
                } else {
                    $value = data_get($row, $field);
                }

                $Sheet->getCell(chr($chr) . $num)->setValueExplicit($value);
                $chr++;
            }
            $num++;
        }

        $fileName = time();
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $fileName . '.xlsx"');
        header("Content-Disposition:attachment;filename={$fileName}.xlsx");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }


    /**
     * 构建搜索
     * @param LoanInfoForm $query
     * @param Request $request
     */
    private function buildSearch(&$query, $request)
    {
        foreach ($this->searchFields as $field) {
            $value = $request->input("search.{$field}");
            if ($value === '') {
                continue;
            }

            $query->where($field, 'like', "{$value}%");
        }
    }

}