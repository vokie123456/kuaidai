<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

/**
 * 用户管理
 * @menu index 用户管理
 * @nodeTitle 用户管理
 * @nodeName index 列表
 */
class UserController extends Controller
{

    public function index()
    {
        $data = array(
            'paginate' => User::orderBy('id', 'desc')->paginate(),
        );

        return view('admin.user.index', $data);
    }

}