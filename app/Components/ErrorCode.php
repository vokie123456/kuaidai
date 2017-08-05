<?php

namespace App\Components;


class ErrorCode
{
    const SYSTEM_ERROR = [
        10001,
        '系统错误'
    ];

    const PERMISSION_DENIED = [
        10002,
        '权限不足'
    ];

    const CAPTCHA_ERROR = [
        200001,
        '验证码错误，请重新获取'
    ];


    const ERR_PARAM = [
        200002,
        '参数有误'
    ];


    const NOT_AUTH = [
        200403,
        '请登录'
    ];

    const ERR_THROTTLE = [
        20429,
        '请求过于频繁，请稍后重试'
    ];

}