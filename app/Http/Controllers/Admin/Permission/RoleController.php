<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Components\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use Illuminate\Http\Request;

/**
 * 角色管理
 * @menu index 角色管理
 * @nodeTitle 权限-角色管理
 * @nodeName index 列表
 * @nodeName store 保存
 * @nodeName update 更新
 * @nodeName destroy 删除
 */
class RoleController extends Controller
{
    /**
     * 节点列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = array(
            'paginate' => AdminRole::paginate()
        );

        return view('admin.permission.role.index', $data);
    }

    /**
     * 保存
     * @param Request $request
     * @return ApiResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, array(
            'role' => ['required', 'unique:admin_roles', 'max:16'],
            'name' => ['required', 'max:16'],
        ));

        AdminRole::create($request->only(['role', 'name']));

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
        $model = AdminRole::findOrFail($id);

        $this->validate($request, array(
            'role' => ['required', "unique:admin_roles,role,{$id}", 'max:16'],
            'name' => ['required', 'max:16'],
        ));

        $data = $request->only(['role', 'name']);
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
        $model = AdminRole::findOrFail($id);

        \DB::beginTransaction();
        $model->delete();
        $model->admins()->detach();
        \DB::commit();

        return ApiResponse::buildFromArray();
    }
}