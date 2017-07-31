<?php

namespace App\Http\Controllers\Admin;

use App\Components\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Message;

/**
 * 留言板
 * @menu index 留言板
 * @nodeTitle 留言板
 * @nodeName index 列表
 * @nodeName destroy 删除
 * @nodeName restore 恢复
 */
class MessageController extends Controller
{

    /**
     * 列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = array(
            'paginate' => Message::withTrashed()->with('user')->orderBy('id', 'desc')->paginate(),
        );

        return view('admin.message.index', $data);
    }

    /**
     * 删除
     * @param $id
     * @return ApiResponse
     */
    public function destroy($id)
    {
        $model = Message::findOrFail($id);

        $model->delete();

        return ApiResponse::buildFromArray();
    }

    /**
     * 恢复
     * @param $id
     * @return ApiResponse
     */
    public function restore($id)
    {
        /** @var Message $model */
        $model = Message::withTrashed()->findOrFail($id);

        $model->restore();

        return ApiResponse::buildFromArray();
    }

}