<?php

namespace App\Models;

/**
 * @property int article_id
 */
class ArticleColumnsRelation extends \Eloquent
{

    const TABLE_NAME = 'article_columns_relations';

    protected $table = self::TABLE_NAME;
}