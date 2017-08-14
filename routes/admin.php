<?php

Route::group(['namespace' => 'Admin'], function() {

    // 登录登出
    Route::get('login', 'AuthController@showLoginForm')->name('login');
    Route::post('login', 'AuthController@login');
    Route::get('logout', 'AuthController@logout')->name('logout');

    // 后台控制器，需要经过admin中间件进行鉴权
    Route::group(['middleware' => ['admin']], function() {

        // 首页
        Route::get('/', 'HomeController@index');
        Route::get('home', 'HomeController@index');

        // UEditor控制器
//        Route::get('ueditor', 'HomeController@ueditor');

        // 修改密码
        Route::get('modify-password', 'AuthController@modifyPasswordForm')->name('modify-password');
        Route::post('modify-password', 'AuthController@modifyPassword')->name('modify-password');

        // 用户管理
        Route::resource('user', 'UserController');

        // 贷款
        Route::group(['prefix' => 'loan'], function() {
            Route::put('product/status/{id}', 'Loan\ProductController@status');
            Route::post('product/uploadLogo', 'Loan\ProductController@uploadLogo');
            Route::put('product/recommend/{id}', 'Loan\ProductController@recommend');
            Route::get('form/export', 'Loan\FormController@export');
            Route::resource('product', 'Loan\ProductController');
            Route::resource('form', 'Loan\FormController');
        });

        // 文章管理
//        Route::patch('article-manage/article/up/{id}', 'ArticleManage\ArticleController@up');
//        Route::patch('article-manage/article/down/{id}', 'ArticleManage\ArticleController@down');
//        Route::post('article-manage/article/preview', 'ArticleManage\ArticleController@preview');
//        Route::post('article-manage/markdown/preview', 'ArticleManage\MarkdownController@preview');
//        Route::patch('article-manage/article/top/{id}', 'ArticleManage\ArticleController@top');
//        Route::patch('article-manage/article/untop/{id}', 'ArticleManage\ArticleController@untop');
//        Route::patch('article-manage/comment/restore/{id}', 'ArticleManage\CommentController@restore');
//        Route::patch('article-manage/comment/deny/{id}', 'ArticleManage\CommentController@deny');
//        Route::resources(array(
//            'article-manage/article' => 'ArticleManage\ArticleController',
//            'article-manage/comment' => 'ArticleManage\CommentController',
//            'article-manage/column' => 'ArticleManage\ColumnController',
//            'article-manage/markdown' => 'ArticleManage\MarkdownController',
//        ));

        // 留言板
//        Route::patch('message/restore/{id}', 'MessageController@restore');
//        Route::resource('message', 'MessageController');

        // 配置选项
        Route::resource('option', 'OptionController');

        // 友情链接
//        Route::resource('link', 'LinkController');
//        Route::resource('deny-keyword', 'DenyKeywordController');

        // 缓存管理
//        Route::delete('cache', 'CacheController@destroy');
//        Route::get('cache', 'CacheController@index');

        // 权限管理
        Route::patch('permission/node/import', 'Permission\NodeController@import');
        Route::patch('permission/menu/import', 'Permission\MenuController@import');
        Route::resources(array(
            'permission/menu' => 'Permission\MenuController',
            'permission/user' => 'Permission\UserController',
            'permission/user.role' => 'Permission\UserRoleController',
            'permission/role' => 'Permission\RoleController',
            'permission/role.permission' => 'Permission\RolePermissionController',
            'permission/node' => 'Permission\NodeController',
        ));
    });

});
