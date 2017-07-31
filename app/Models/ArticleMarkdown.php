<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property Article article
 * @property string content
 */
class ArticleMarkdown extends \Eloquent
{

    use SoftDeletes;

    protected $fillable = [
        'article_id', 'content'
    ];

    /**
     * 关联Article
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function article()
    {
        return $this->hasOne(Article::class, 'id', 'article_id');
    }

}
