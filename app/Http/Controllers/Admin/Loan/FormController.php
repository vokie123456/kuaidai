<?php

namespace App\Http\Controllers\Admin\Loan;

use App\Http\Controllers\Controller;
use App\Models\LoanInfoForm;

/**
 * 贷款信息管理
 * @menu index 贷款信息管理
 * @nodeTitle 贷款信息管理
 * @nodeName index 列表
 */
class FormController extends Controller
{

    /**
     * 贷款信息
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = array(
            'paginate' => LoanInfoForm::with('user', '_extends')->orderBy('id', 'desc')->paginate(),
            'baseData' => config('loan'),
        );

        return view('admin.loan.form.index', $data);
    }

}