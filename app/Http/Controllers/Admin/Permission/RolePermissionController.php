<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Components\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AdminNode;
use App\Models\AdminRole;
use Illuminate\Http\Request;

/**
 * 角色权限管理
 * @nodeTitle 权限-角色权限管理
 * @nodeName index 列表
 * @nodeName store 保存或更新
 */
class RolePermissionController extends Controller
{

    /**
     * 角色权限列表
     * @param $roleId
     * @return ApiResponse
     */
    public function index($roleId)
    {
        $role = AdminRole::with('permissions')->findOrFail($roleId);
        $allNodes = AdminNode::groupByCtlAll();

        $data = array(
            'checked' => array_column($role->permissions->toArray(), 'id'),
            'all' => $allNodes
        );

        return (new ApiResponse($data));
    }

    /**
     * 保存/更新角色权限
     * @param Request $request
     * @param $roleId
     * @return ApiResponse
     */
    public function store(Request $request, $roleId)
    {
        $model = AdminRole::findOrFail($roleId);
        \DB::beginTransaction();
        $model->permissions()->detach();
        $model->permissions()->attach($request->input('node_id'));
        \DB::commit();

        return ApiResponse::buildFromArray();
    }

}