<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Components\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use App\Models\AdminNode;
use App\Services\PermissionImportService;
use Illuminate\Http\Request;

/**
 * 菜单管理
 * @menu index 菜单管理
 * @nodeTitle 权限-菜单管理
 * @nodeName index 列表
 * @nodeName store 保存
 * @nodeName update 更新
 * @nodeName destroy 删除
 * @nodeName import 一键导入未注册菜单
 */
class MenuController extends Controller
{

    /**
     * 节点列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = array(
            'paginate' => AdminMenu::with('node')->paginate(),
            'permissions' => AdminNode::all(),
        );

        return view('admin.permission.menu.index', $data);
    }

    /**
     * 保存
     * @param Request $request
     * @return ApiResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, array(
            'pid' => ['required', 'numeric'],
            'name' => ['required', 'max:16'],
            'weight' => ['required', 'numeric', 'max:100', 'min:0'],
            'route' => ['max:255'],
            'node_id' => ['required', 'numeric']
        ));

        AdminMenu::create($request->only(['pid', 'name', 'weight', 'route', 'node_id']));

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
        $model = AdminMenu::findOrFail($id);

        $this->validate($request, array(
            'pid' => ['required', 'numeric'],
            'name' => ['required', 'max:16'],
            'weight' => ['required', 'numeric', 'max:100', 'min:0'],
            'route' => ['max:255'],
            'node_id' => ['required', 'numeric']
        ));

        $data = $request->only(['pid', 'name', 'weight', 'route', 'node_id']);
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
        $model = AdminMenu::findOrFail($id);
        $model->delete();

        return ApiResponse::buildFromArray();
    }

    /**
     * 一键导入未注册菜单
     * @param PermissionImportService $service
     * @return ApiResponse
     */
    public function import(PermissionImportService $service)
    {
        $service->importMenu();

        return ApiResponse::buildFromArray();
    }

}