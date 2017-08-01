<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

/**
 * 应用入口
 */
class HomeController extends Controller
{

    public function index()
    {
        return view('web.index');
    }

}