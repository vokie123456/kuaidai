<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Components\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

/**
 * 用户管理
 * @menu index 用户管理
 * @nodeTitle 权限-用户管理
 * @nodeName index 列表
 * @nodeName store 保存
 * @nodeName update 更新
 * @nodeName destroy 删除
 */
class UserController extends Controller
{

    /**
     * 节点列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = array(
            'paginate' => Admin::with('roles')->paginate()
        );

        return view('admin.permission.user.index', $data);
    }

    /**
     * 保存
     * @param Request $request
     * @return ApiResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, array(
            'username' => ['required', 'min:4', 'max:16', 'unique:admins'],
            'name' => ['required', 'max:12'],
            'password' => ['required', 'min:6', 'max:32'],
        ));

        $data = $request->only(['username', 'name', 'password']);
        $data['password'] = bcrypt($data['password']);

        Admin::create($data);

        return ApiResponse::buildFromArray();
    }

    /**
     * 更新
     * @param Request $request
     * @param $id
     * @return ApiResponse
     */
    public function update(Request $request, $id)
    {
        $model = Admin::findOrFail($id);

        $this->validate($request, array(
            'username' => ['required', 'min:4', 'max:16', "unique:admins,username,{$id}"],
            'name' => ['required', 'max:12'],
            'password' => ['min:6', 'max:32'],
        ));

        $data = $request->only(['username', 'name']);

        // 判断是否需要修改密码
        if ($request->input('password')) {
            $data['password'] = bcrypt($request->input('password'));
        }

        $model->update($data);

        return ApiResponse::buildFromArray();
    }

    /**
     * 删除
     * @param $id
     * @return ApiResponse
     */
    public function destroy($id)
    {
        $model = Admin::findOrFail($id);
        \DB::beginTransaction();
        $model->delete();
        $model->roles()->detach();
        \DB::commit();

        return ApiResponse::buildFromArray();
    }

}