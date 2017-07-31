<?php

namespace App\Http\Controllers\Blog;

use App\Components\CacheName;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TagController extends Controller
{

    /**
     * 标签详情
     * @param Request $request
     * @param $tag
     * @return mixed
     */
    public function detail(Request $request, $tag)
    {
        $hKey = md5((int)$request->input('page') . $tag);
        $redis = \RedisClient::connection();
        $key = config('cache.prefix') . ':' . CacheName::PAGE_TAG[0];
        if (!$redis->hexists($key, $hKey) || config('app.debug')) {
            $data = array(
                'pageName' => $tag,
                'tag' => $tag,
                'columnId' => null,
            );

            $redis->hset($key, $hKey, view('jiestyle2.tag', $data)->render());
        }

        return $redis->hget($key, $hKey);
    }


}