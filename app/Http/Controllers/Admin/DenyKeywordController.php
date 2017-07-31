<?php

namespace App\Http\Controllers\Admin;

use App\Components\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\DenyKeyword;
use Illuminate\Http\Request;

/**
 * 禁用词管理
 * @menu index 禁用词管理
 * @nodeTitle 禁用词管理
 * @nodeName index 列表
 * @nodeName store 保存
 * @nodeName destroy 删除
 */
class DenyKeywordController extends Controller
{

    protected $trimInputs = ['keyword'];

    /**
     * 列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = array(
            'paginate' => DenyKeyword::paginate(100),
        );

        return view('admin.deny-keyword.index', $data);
    }

    /**
     * 新增
     * @param Request $request
     * @return ApiResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, array(
            'keyword' => ['required'],
        ));

        $allKeywords = explode("\n", str_replace(["\r\n", "\r"], "\n", $request->input('keyword')));
        $keywords = [];
        array_walk($allKeywords, function($value) use (&$keywords) {
            $value = str_replace([' ', "\t", "\n", "\r", "\0", "\x0B"], '', $value);
            if (!empty($value) && !in_array($value, $keywords)) {
                $keywords[] = $value;
            }
        });

        if (!empty($keywords)) {
            $existsKeywords = array_column(DenyKeyword::whereIn('keyword', $keywords)->get()->toArray(), 'keyword');
            $newKeywords = [];
            array_walk($keywords, function($keyword) use (&$newKeywords, $existsKeywords) {
                if (!in_array($keyword, $existsKeywords)) {
                    $newKeywords[] = array('keyword' => $keyword);
                }
            });

            DenyKeyword::insert($newKeywords);
        }

        return ApiResponse::buildFromArray();
    }

    /**
     * 删除
     * @param $keyword
     * @return ApiResponse
     */
    public function destroy($keyword)
    {
        DenyKeyword::where('keyword', $keyword)
            ->delete();

        return ApiResponse::buildFromArray();
    }

}