<?php

namespace App\Http\Controllers\Blog;

use App\Components\CacheName;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * 首页
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $hKey = (int)$request->input('page');
        $redis = \RedisClient::connection();
        $key = config('cache.prefix') . ':' . CacheName::PAGE_HOME[0];
        if (!$redis->hexists($key, $hKey) || config('app.debug')) {
            $data = array(
                'pageName' => '首页',
                'columnId' => null
            );

            $redis->hset($key, $hKey, view('jiestyle2.list', $data)->render());
        }

        return $redis->hget($key, $hKey);
    }

}