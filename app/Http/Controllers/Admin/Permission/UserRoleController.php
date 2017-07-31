<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Components\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminRole;
use Illuminate\Http\Request;

/**
 * 用户角色管理
 * @nodeTitle 权限-用户角色管理
 * @nodeName index 列表
 * @nodeName store 保存或更新
 */
class UserRoleController extends Controller
{

    /**
     * 角色权限列表
     * @param $adminId
     * @return ApiResponse
     */
    public function index($adminId)
    {
        $admin = Admin::with('roles')->findOrFail($adminId);
        $allRoles = AdminRole::all();

        $data = array(
            'checked' => array_column($admin->roles->toArray(), 'id'),
            'all' => $allRoles
        );

        return (new ApiResponse($data));
    }

    /**
     * 保存/更新角色权限
     * @param Request $request
     * @param $adminId
     * @return ApiResponse
     */
    public function store(Request $request, $adminId)
    {
        $model = Admin::findOrFail($adminId);
        \DB::beginTransaction();
        $model->roles()->detach();
        $model->roles()->attach($request->input('role_id'));
        \DB::commit();

        return ApiResponse::buildFromArray();
    }

}