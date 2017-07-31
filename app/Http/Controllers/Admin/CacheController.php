<?php

namespace App\Http\Controllers\Admin;

use App\Components\ApiResponse;
use App\Components\CacheName;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * 缓存管理
 * @menu index 缓存管理
 * @nodeTitle 缓存管理
 * @nodeName index 列表
 * @nodeName destroy 清除
 */
class CacheController extends Controller
{

    /**
     * 列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $keys = array_values(CacheName::getAllKey());
        $pages = array();
        $page = 3;

        foreach ($keys as $i => $key) {
            $pageSize = $i % $page;
            if (!isset($pages[$pageSize])) {
                $pages[$pageSize] = array();
            }

            $pages[$pageSize][$key[0]] = $key[1];
        }

        $data = array(
            'pages' => $pages,
        );

        return view('admin.cache.index', $data);
    }


    /**
     * 清除缓存
     * @param Request $request
     * @return ApiResponse
     */
    public function destroy(Request $request)
    {
        $this->validate($request, array(
            'key' => ['required', 'array']
        ));

        CacheName::clear($request->input('key'));

        return ApiResponse::buildFromArray();
    }

}