<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'Blog'], function() {
    // 首页
    Route::get('/', 'HomeController@index');

    // 博客
    Route::get('blog/{id}', 'BlogController@detail');

    // 栏目
    Route::get('column/{alias}', 'ColumnController@detail');

    // 标签
    Route::get('tag/{tag}', 'TagController@detail');

    // 统计
    Route::get('stat/pv/{article_id}', 'StatController@pv');

    // 发布留言
    Route::post('message', 'MessageController@store');
});
