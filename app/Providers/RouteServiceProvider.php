<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapAdminRoutes();

        $this->mapWebRoutes();
    }

    /**
     * 管理后台路由
     */
    public function mapAdminRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
            'domain' => config('domain.admin'),
        ], function ($router) {
            require base_path('routes/admin.php');
        });
    }

    /**
     * Web前端路由
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
            'domain' => config('domain.web'),
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * 获取路由命名空间
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}
