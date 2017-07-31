<?php

namespace App\Http\Controllers\Admin;

use App\Components\UEditor\UEditor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * 首页入口
 * @menu index 首页
 * @nodeTitle 首页入口
 * @nodeName index 首页
 * @nodeName ueditor UEditor控制器
 */
class HomeController extends Controller
{

    /**
     * 后台入口
     */
    public function index()
    {
        $redisInfo = \RedisClient::connection()->info();

        $data = array(
            'redis' => $redisInfo
        );

        return view('admin.home', $data);
    }

    /**
     * UE控制器
     * @param Request $request
     * @return mixed|string
     */
    public function ueditor(Request $request)
    {
        $action = $request->input('action');
        $callback = $request->input('callback');

        return (new UEditor($request))->callAction($action, $callback);
    }

}