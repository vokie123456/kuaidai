<?php

namespace App\Http\Controllers\Web;

use App\Components\ApiResponse;
use App\Components\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\LoanProducts;
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
        $fields = ['id', 'name', 'logo', 'loan_limit_min', 'loan_limit_max', 'deadline_min', 'deadline_max', 'deadline_type', 'loaneders'];
        $cases = LoanProducts::select($fields)
            ->where('status', 1)
            ->get()
            ->toArray();

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
        $case = LoanProducts::find($id);

        if (empty($case)) {
            return ApiResponse::buildFail(ErrorCode::ERR_PARAM[0], '方案不存在！');
        }

        $case = $case->toArray();
        $case['condition'] = explode("\n", $case['condition']);
        $case['process'] = explode("\n", $case['process']);
        $case['remind'] = explode("\n", $case['remind']);
        $case['detail'] = str_replace("\n", '<br>', $case['detail']);

        $data = array(
            'id' => $id,
            'case' => $case,
        );

        return ApiResponse::buildSuccess($data);
    }

}