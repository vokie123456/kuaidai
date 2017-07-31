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

Route::get('test', function(\App\Components\Sms $sms) {
    $code = rand(1000, 9999);
    return $sms->send('13632377543', "【借贷专家】您的验证码是：{$code}。");
});

Route::group(['namespace' => 'Web'], function() {

    // 登录
    Route::get('login', 'AuthController@loginView');
    Route::post('login', 'AuthController@login');

    // 获取验证码
    Route::post('captcha', 'AuthController@captcha')->middleware('api_throttle:10,10');

});
