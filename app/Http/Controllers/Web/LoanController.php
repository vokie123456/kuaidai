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
     */
    public function cases()
    {

    }

}