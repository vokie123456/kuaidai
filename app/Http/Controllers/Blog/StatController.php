<?php

namespace App\Http\Controllers\Blog;

use App\Components\CacheName;
use App\Http\Controllers\Controller;
use App\Models\Article;

class StatController extends Controller
{

    /**
     * 统计PV
     * @param $id
     * @return string
     */
    public function pv($id)
    {
        $redis = \RedisClient::connection();
        $key = \Cache::getPrefix() . CacheName::STAT_PV[0];

        $pv = (int)$redis->hincrby($key, $id, 1);
        if ($pv % 10 == 0 || $pv == 1) {
            try {
                /** @var Article $article */
                $article = Article::findOrFail($id);
                if ($article && $article->pv < $pv) {
                    $article->pv = $pv;
                    $article->saveOrFail();
                } else {
                    $pv = $article->pv + 1;
                    $redis->hset($key, $id, $pv);
                }
            } catch (\Exception $e) {
            } catch (\Throwable $t) {
            }
        }

        return sprintf('document.getElementById("pv").innerHTML = %d', $pv);
    }

}