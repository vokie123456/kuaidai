<?php

namespace App\Http\Controllers\Web;

use App\Components\ApiResponse;
use App\Components\ErrorCode;
use App\Http\Controllers\Controller;
use Auth;
use Closure;

/**
 * 借贷
 */
class LoanController extends Controller
{
    /** @var \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard */
    private $auth;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->auth = Auth::guard('web');

        // 验证登录
        $this->middleware(function($request, Closure $next) {
            if (!$this->auth->check()) {
                return ApiResponse::buildFromArray(ErrorCode::NOT_AUTH);
            }

            return $next($request);
        });
    }

    /**
     * 借贷方案
     * @return ApiResponse
     */
    public function cases()
    {
        $cases = array();

        for ($i = 0; $i < 10; $i++) {
            $cases[] = array(
                'id' => $i,
                'icon' => '/images/web/cases1.png',
                'title' => '闪电借贷' .rand(10, 99),
                'loan_num' => rand(100, 999),// 放款人数
                'loan_limit' => rand(100, 200) . '-' . rand(300, 400) . '元',
                'deadline' => rand(1,3) . '-' . rand(4,6) . '月',
                'monthly_rate' => rand(1, 4) / 100,
            );
        }

        $data = array(
            'cases' => $cases
        );

        return ApiResponse::buildSuccess($data);
    }

    /**
     * 详情
     * @param $id
     * @return ApiResponse
     */
    public function case($id)
    {
        $case = array(
            'icon' => '/images/web/cases1.png',
            'title' => '闪电借贷',
            'loan_num' => 100,// 放款人数
            'loan_limit' => rand(100, 200) . '-' . rand(300, 400) . '元',
            'deadline' => rand(1,3) . '-' . rand(4,6) . '月',
            'monthly_rate' => rand(1, 4) / 100,
            'condition' => '条件',
            'process' => '流程',
            'audit_instructions' => '审核说明',
            'remind' => '关键提醒',
            'detail' => '详情介绍',
        );

        $data = array(
            'id' => $id,
            'case' => $case,
        );

        return ApiResponse::buildSuccess($data);
    }

}