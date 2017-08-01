<?php

namespace App\Http\Controllers\Web;

use App\Components\ApiResponse;
use App\Components\CacheName;
use App\Components\ErrorCode;
use App\Components\Sms;
use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Cache;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller
{
    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        Validator::extend('mobile', function($attribute, $value, $parameters) {
            $patter = '/^1[34578]\d{9}$/';
            return (preg_match($patter, $value)) ? true : false;
        });
    }

    /**
     * 登录
     * @param Request $request
     * @return ApiResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, array(
            'mobile' => ['required', 'mobile'],
            'captcha' => ['required']
        ));

        $mobile = $request->input('mobile');
        $code = Cache::pull(CacheName::LOGIN_CAPTCHA[0] . ":{$mobile}");
        if ($request->input('captcha') != $code) {
            return ApiResponse::buildFromArray(ErrorCode::CAPTCHA_ERROR);
        }

        $user = User::findByUsernameOrCreate($mobile, [
            'username' => $mobile,
            'password' => '',
            'nickname' => $mobile
        ]);

        Auth::guard('web')->login($user);

        return ApiResponse::buildFromArray();
    }

    /**
     * 获取验证码
     * @param Request $request
     * @param Sms $sms
     * @return ApiResponse
     */
    public function captcha(Request $request, Sms $sms)
    {
        $this->validate($request, array(
            'mobile' => ['required', 'mobile']
        ), array(
            'mobile.mobile' => '手机格式错误',
        ));

        $code = rand(100000, 999999);
        $mobile = $request->input('mobile');
        Cache::put(CacheName::LOGIN_CAPTCHA[0] . ":{$mobile}", $code, 30);
        $msg = "【借贷专家】您的验证码是：{$code}。";

        $sms->send($mobile, $msg);

        return ApiResponse::buildFromArray();
    }

}