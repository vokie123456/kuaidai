<?php

namespace App\Http\Controllers\Web;

use App\Components\ApiResponse;
use App\Components\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Closure;
use Illuminate\Http\Request;

/**
 * 用户
 */
class UserController extends Controller
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
     * 我的
     * @return ApiResponse
     */
    public function me()
    {
        $auth = Auth::guard('web');
        if ($auth->check()) {
            /** @var User $user */
            $user = Auth::guard('web')->user();
            return ApiResponse::buildSuccess([
                'user' => array(
                    'id' => $user->id,
                    'username' => $user->username,
                    'nickname' => $user->nickname,
                    'avatar' => '/images/icon/avatar.png',
                )
            ]);
        } else {
            return ApiResponse::buildFromArray(ErrorCode::NOT_AUTH);
        }
    }

    /**
     * 更新用户信息
     * @param Request $request
     * @return ApiResponse
     */
    public function setUserInfo(Request $request)
    {
        $this->validate($request, array(
            'nickname' => ['required', 'between:2,16']
        ));
        /** @var User $user */
        $user = $this->auth->user();
        $user->nickname = $request->input('nickname');
        $user->saveOrFail();

        return ApiResponse::buildFromArray();
    }

}