<?php

namespace App\Http\Controllers\Blog;

use App\Components\CacheName;
use App\Http\Controllers\Controller;
use App\Models\Article;

class BlogController extends Controller
{

    /**
     * 文章详情
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        $key = \Cache::getPrefix() . CacheName::PAGE_ARTICLE[0];
        $redis = \RedisClient::connection();

        if (!$redis->hexists($key, $id) || config('app.debug')) {
            $article = Article::with('contents', 'tags', 'columns')
                ->where('status', Article::STATUS_RELEASE)
                ->findOrFail($id);

            $data = array(
                'article' => $article,
                'blogKeywords' => implode(',', array_column($article['tags']->toArray(), 'tag')),
                'blogDescription' => $article['title'],
                'pageName' => $article['title']
            );

            $page = view('jiestyle2.detail', $data)->render();

            $redis->hset($key, $id, $page);
        }

        return $redis->hget($key, $id);
    }

}