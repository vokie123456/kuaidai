<?php

namespace App\Http\Controllers\Admin\ArticleManage;

use App\Components\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\ArticleColumn;
use Illuminate\Http\Request;

/**
 * 栏目管理
 * @menu index 栏目列表
 * @nodeTitle 栏目管理
 * @nodeName index 列表
 * @nodeName store 保存
 * @nodeName update 更新
 * @nodeName destroy 删除
 */
class ColumnController extends Controller
{

    protected $trimInputs = ['column_name', 'alias', 'view'];

    public function __construct()
    {
        parent::__construct();
        \Validator::extend('view_exists', $this->validatorViewExists());
        \Validator::replacer('view_exists', function($message, $attribute, $rule, $parameters) {
            return str_replace(':view', \Request::input($attribute), $message);
        });
    }

    /**
     * 验证视图是否存在
     * @return \Closure
     */
    private function validatorViewExists()
    {
        return function($attribute, $value, $parameters) {
            return \View::exists($value);
        };
    }

    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $where = array();
        if ($request->has('parent_id')) {
            $where['parent_id'] = (int)$request->input('parent_id');
        }

        $data = array(
            'paginate' => ArticleColumn::where($where)->paginate(),
        );

        return view('admin.article-manage.column.index', $data);
    }

    /**
     * 保存
     * @param Request $request
     * @return ApiResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, array(
            'alias' => ['required', 'unique:article_columns,alias,NULL,id,deleted_at,NULL', 'max:10'],
            'column_name' => ['required', 'max:8'],
            'weight' => ['required', 'numeric', 'max:100', 'min:0'],
            'is_show' => ['required', 'in:1,2'],
            'type' => ['required', 'in:1,2,3'],
            'parent_id' => ['required', 'numeric', 'min:0'],
            'view' => ['required_if:type,3', 'max:100', 'view_exists'],
        ));

        ArticleColumn::store($request->only(['column_name', 'alias', 'weight', 'is_show', 'type', 'parent_id', 'view']));

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
        $this->validate($request, array(
            'alias' => ['required', "unique:article_columns,alias,{$id},id,deleted_at,NULL", 'max:10'],
            'column_name' => ['required', 'max:8'],
            'weight' => ['required', 'numeric', 'max:100', 'min:0'],
            'is_show' => ['required', 'in:1,2'],
            'type' => ['required', 'in:1,2,3'],
            'parent_id' => ['required', 'numeric', 'min:0'],
            'view' => ['required_if:type,3', 'max:100', 'view_exists'],
        ));

        $data = $request->only(['column_name', 'alias', 'weight', 'is_show', 'type', 'parent_id', 'view']);

        ArticleColumn::updateById($id, $data);

        return ApiResponse::buildFromArray();
    }

    /**
     * 删除
     * @param $id
     * @return ApiResponse
     */
    public function destroy($id)
    {
        ArticleColumn::deleteById($id);

        return ApiResponse::buildFromArray();
    }

}