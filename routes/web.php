<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'Web'], function() {

    // 本地环境自动登录
    if (config('app.env') == 'local') {
        $user = \App\Models\User::first();
        Auth::guard('web')->login($user);
    }

    Route::get('', 'HomeController@index');

    // 用户
    Route::group(['prefix' => 'user'], function() {
        // 我的
        Route::get('me', 'UserController@me');

        // 更新个人信息
        Route::post('set-user-info', 'UserController@setUserInfo');
    });

    // 借贷
    Route::group(['prefix' => 'loan'], function() {
        // 借贷方案
        Route::get('cases', 'LoanController@cases');
        Route::get('case/{id}', 'LoanController@case');
    });

    // 登录
    Route::post('login', 'AuthController@login');

    // 获取验证码
    Route::post('captcha', 'AuthController@captcha')->middleware('api_throttle:10,10');

});
