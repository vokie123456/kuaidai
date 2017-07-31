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

        $this->mapBlogRoutes();

        $this->mapApiRoutes();
        //
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
     * 博客地址
     */
    protected function mapBlogRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/blog.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
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
