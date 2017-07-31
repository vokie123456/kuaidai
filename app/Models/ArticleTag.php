<?php

namespace App\Models;

use App\Components\CacheName;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleTag extends \Eloquent
{

    public $timestamps = false;

    protected $fillable = [
        'tag', 'article_id'
    ];

    /**
     * 生成新模型
     * @param $articleId
     * @param array $tag
     * @return array
     */
    public static function getNewTags($articleId, $tag)
    {
        if (empty($tag) || !is_array($tag)) {
            return [];
        }

        $tags = array();

        foreach ($tag as $t) {
            $attr = array(
                'tag' => $t,
                'article_id' => $articleId
            );

            $tags[] = new self($attr);
        }

        return $tags;
    }

    /**
     * 关联文章
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function article()
    {
        return $this->hasOne(Article::class, 'id', 'article_id');
    }

    /**
     * 获取所有标签
     * @return array
     */
    public static function getAllTag()
    {
        return self::groupBy('tag')->get()->toArray();
    }
}