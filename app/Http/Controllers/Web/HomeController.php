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
        $data = array(
            'global' => array(
                'appName' => config('app.name'),
                'extend' => config('loan.extend'),
                'jobs' => config('loan.jobs'),
            )
        );
        return view('web.index', $data);
    }

}