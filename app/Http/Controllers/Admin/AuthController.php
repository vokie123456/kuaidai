<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

/**
 * 鉴权
 * @nodeTitle 鉴权
 * @nodeName modifyPasswordForm 修改密码页面
 * @nodeName modifyPassword 修改密码逻辑
 */
class AuthController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', array(
            'except' => [
                'logout',
                'modifyPasswordForm',
                'modifyPassword'
            ]
        ));
        parent::__construct();
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return \Auth::guard('admin');
    }

    /**
     * 修改密码表单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function modifyPasswordForm()
    {
        return view('admin.auth.modify-password');
    }

    /**
     * 修改密码逻辑
     * @param Request $request
     * @return string
     */
    public function modifyPassword(Request $request)
    {
        // 生成新的字段，用于语言包自动识别
        $request->request->add(array(
            'new_password' => $request->input('password')
        ));

        \Validator::extend('attempt', $this->validatorAttempt());

        $this->validate($request, array(
            'old_password' => ['required', 'attempt'],
            'new_password' => ['required', 'max:16', 'min:6'],
            'password' => 'confirmed'
        ));

        /** @var \App\Models\Admin $user */
        $user = $this->guard()->user();
        $user->password = bcrypt($request->input('new_password'));
        $user->saveOrFail();

        return $this->modifyPasswordForm()->with('success', '修改成功');
    }

    /**
     * 验证密码
     * @return \Closure
     */
    private function validatorAttempt()
    {
        return function($attribute, $value, $parameters) {
            /** @var \Illuminate\Auth\SessionGuard $guard */
            $guard = $this->guard();
            $username = $this->username();

            return $guard->getProvider()->validateCredentials($guard->user(), array(
                $username => $this->guard()->user()->$username,
                'password' => $value
            ));
        };
    }

}