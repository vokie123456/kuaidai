<?php

namespace App\Models;

class ArticleContent extends \Eloquent
{
    /** Mysql-text长度 */
    const TEXT_LEN = 65535;

    protected $fillable = [
        'article_id', 'content'
    ];

    /**
     * 切割内容并返回模型列表
     * @param $articleId
     * @param $content
     * @return static[]
     */
    public static function cut($articleId, $content)
    {
        // 内容切割，text的最大存储是65535，所以切割长度要<=该值
        $size = self::TEXT_LEN - 100;
        $maxPage = ceil(mb_strlen($content) / $size);
        $contents = array();

        for ($i = 0; $i < $maxPage; $i++) {
            $attr = array(
                'article_id' => $articleId,
                'content' => mb_substr($content, $i * $size, $size)
            );

            $contents[] = new self($attr);
        }

        return $contents;
    }
}
