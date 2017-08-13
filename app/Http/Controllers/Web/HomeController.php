<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LoanProduct;

/**
 * 应用入口
 */
class HomeController extends Controller
{

    public function index()
    {
        $products = LoanProduct::select(['name', 'loan_limit_max'])
            ->where('status', LoanProduct::STATUS_ENABLED)
            ->get()
            ->toArray();

        $data = array(
            'products' => $products,
            'global' => array(
                'appName' => config('app.name'),
                'extend' => config('loan.extend'),
                'jobs' => config('loan.jobs'),
            )
        );
        return view('web.index', $data);
    }

}