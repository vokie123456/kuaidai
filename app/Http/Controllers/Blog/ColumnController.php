<?php

namespace App\Http\Controllers\Blog;

use App\Components\CacheName;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleColumn;
use App\Models\ArticleColumnsRelation;

class ColumnController extends Controller
{

    /**
     * 栏目详情
     * @param $alias
     * @return mixed
     */
    public function detail($alias)
    {
        $redis = \RedisClient::connection();
        $key = config('cache.prefix') . ':' . CacheName::PAGE_COLUMN[0];
        $hKey = md5($alias);

        if (!$redis->hexists($key, $hKey) || config('app.debug')) {
            /** @var ArticleColumn $column */
            $column = ArticleColumn::findByAliasOrFail($alias);

            if ($column->type == ArticleColumn::TYPE_PAGE) {
                $page = $this->detailView($column);
            } elseif ($column->type == ArticleColumn::TYPE_VIEW) {
                return $this->viewView($column);// 不能使用缓存，否则会导致页面不更新
            } else {
                $page = $this->listView($column);
            }

            $redis->hset($key, $hKey, $page->render());
        }

        return $redis->hget($key, $hKey);
    }

    /**
     * 栏目详情页
     * @param ArticleColumn $column
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function detailView($column)
    {
        /** @var ArticleColumnsRelation $articleRelation */
        $articleRelation = ArticleColumnsRelation::select('article_id')
            ->where('column_id', $column->id)
            ->orderBy('article_id', 'desc')
            ->firstOrFail();

        $article = Article::with('contents', 'tags', 'columns')
            ->where('status', Article::STATUS_RELEASE)
            ->findOrFail($articleRelation->article_id);

        $data = array(
            'article' => $article,
            'blogKeywords' => implode(',', array_column($article['tags']->toArray(), 'tag')),
            'blogDescription' => $article['title'],
            'pageName' => $article['title']
        );

        return view('jiestyle2.column_detail', $data);
    }

    /**
     * 栏目文章列表
     * @param ArticleColumn $column
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function listView($column)
    {
        $data = array(
            'pageName' => $column->column_name,
            'columnId' => $column->id,
        );

        return view('jiestyle2.list', $data);
    }

    /**
     * 视图
     * @param $column
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function viewView($column)
    {
        return view($column->view, ['column' => $column]);
    }

}