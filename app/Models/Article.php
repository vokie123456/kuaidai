<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property int status
 * @property int pv
 * @property ArticleColumn columns
 * @property string title
 * @property string author
 * @property int type
 * @property string write_time
 * @property int cover_type
 * @property string cover_url
 * @property ArticleTag tags
 */
class Article extends \Eloquent
{
    use SoftDeletes;

    /** 状态-发布 */
    const STATUS_RELEASE = 1;

    /** 状态-下线 */
    const STATUS_DRAFT = 2;

    /** 类型-文章 */
    const TYPE_ARTICLE = 1;

    /** 类型-页面 */
    const TYPE_PAGE = 2;

    /** 封面类型-无 */
    const COVER_TYPE_NONE = 1;

    /** 封面类型-小图 */
    const COVER_TYPE_SMALL = 2;

    /** 封面类型-大图 */
    const COVER_TYPE_BIG = 3;

    /** 置顶 */
    const IS_TOP = 1;

    /** 不置顶 */
    const UN_TOP = 2;

    protected $fillable = [
        'title', 'user_id', 'author', 'status', 'type', 'excerpt', 'author', 'write_time', 'cover_type', 'cover_url'
    ];

    /** 需要额外显示的字段 @var array */
    protected $appends = ['status_text', 'type_text'];

    /**
     * 关联内容
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents()
    {
        return $this->hasMany(ArticleContent::class);
    }

    /**
     * 关联栏目
     */
    public function columns()
    {
        return $this->belongsToMany(ArticleColumn::class, 'article_columns_relations', 'article_id', 'column_id');
    }

    /**
     * 添加文章
     * @param array $data
     * @param $column
     * @param $tag
     * @param $content
     * @return static
     */
    public function add(array $data, $column, $tag, $content)
    {
        return \DB::transaction(function() use ($data, $column, $tag, $content) {
            /** @var static $model */
            $model = self::create($data);

            list($contents, $columns, $tags) = $this->getRelationsData($model->id, $content, $column, $tag, $data['write_time']);

            $model->contents()->saveMany($contents);
            $model->columns()->attach($columns);
            $model->tags()->saveMany($tags);

            return $model;
        });
    }

    /**
     * 更新文章
     * @param $data
     * @param $column
     * @param $tag
     * @param $content
     * @return static
     */
    public function put(array $data, $column, $tag, $content)
    {
        return \DB::transaction(function () use ($data, $column, $tag, $content) {
            $this->update($data);

            list($contents, $columns, $tags) = $this->getRelationsData($this->id, $content, $column, $tag, $data['write_time']);

            $this->tags()->delete();
            $this->contents()->delete();
            $this->columns()->detach();

            $this->contents()->saveMany($contents);
            $this->columns()->attach($columns);
            $this->tags()->saveMany($tags);

            return $this;
        });
    }

    /**
     * 获取关联数据
     * @param $id
     * @param $content
     * @param $column
     * @param $tag
     * @param $writeTime
     * @return array
     */
    private function getRelationsData($id, $content, $column, $tag, $writeTime)
    {
        $contents = array();
        if (!empty($content)) {
            $contents = ArticleContent::cut($id, $content);
        }

        $columns = array();
        if (!empty($column)) {
            $columns = ArticleColumn::whereIn('id', $column)->get();
        }

        $tags = array();
        if (!empty($tag)) {
            $tags = ArticleTag::getNewTags($id, $tag);
        }

        return [$contents, $columns, $tags];
    }

    /**
     * 关联标签
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags()
    {
        return $this->hasMany(ArticleTag::class);
    }

    /**
     * 发布
     * @param $id
     * @return bool
     */
    public static function up($id)
    {
        return self::patch($id, array('status' => self::STATUS_RELEASE));
    }

    /**
     * 下线
     * @param $id
     * @return bool
     */
    public static function down($id)
    {
        return self::patch($id, array('status' => self::STATUS_DRAFT));
    }

    /**
     * 置顶操作
     * @param $id
     * @return bool
     */
    public static function setTop($id)
    {
        return self::patch($id, array('is_top' => self::IS_TOP));
    }

    /**
     * 取消置顶操作
     * @param $id
     * @return bool
     */
    public static function unsetTop($id)
    {
        return self::patch($id, array('is_top' => self::UN_TOP));
    }

    /**
     * 更新
     * @param $id
     * @param array $data
     * @return bool
     */
    private static function patch($id, array $data)
    {
        $article = self::findOrFail($id);

        foreach ($data as $key => $value) {
            $article->$key = $value;
        }

        return $article->saveOrFail();
    }

    /**
     * 删除
     * @param $id
     * @param \Closure|null $after
     * @return int
     */
    public static function del($id, \Closure $after = null)
    {
        /** @var self $model */
        $model = self::findOrFail($id);

        \DB::beginTransaction();
        $model->delete();
        $model->contents()->delete();

        if (!is_null($after)) {
            $after($id);
        }

        \DB::commit();

        return self::destroy($id);
    }

    /**
     * 通过栏目ID获取文章列表
     * @param int|null $columnId 栏目ID，若获取全部文章，传null
     * @param int $pageSize
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getColumnArticles($columnId, $pageSize = 30)
    {
        $query = self::select('articles.*')
            ->join(ArticleColumnsRelation::TABLE_NAME, 'article_id', '=', 'id')
            ->where('status', self::STATUS_RELEASE)
            ->where('type', self::TYPE_ARTICLE)
            ->orderBy('write_time', 'desc')
            ->orderBy('id', 'desc')
            ->with('columns', 'tags');

        if (!is_null($columnId)) {
            $query->where('column_id', $columnId);
        }

        return $query->paginate($pageSize);
    }

    /**
     * 通过标签名称获取文章列表
     * @param $tag
     * @param int $pageSize
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getTagArticles($tag, $pageSize = 30)
    {
        $tabAt = (new ArticleTag())->getTable();
        return self::select('articles.*')
            ->join("{$tabAt}", 'article_id', '=', 'articles.id')
            ->where("{$tabAt}.tag", $tag)
            ->orderBy('write_time', 'desc')
            ->orderBy('id', 'desc')
            ->with('columns', 'tags')
            ->paginate($pageSize);
    }

    /**
     * 通过栏目ID获取置顶文章列表
     * @param int|null $columnId 栏目ID，若获取全部文章，传null
     * @param int $count
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getColumnTopArticles($columnId, $count = 3)
    {
        $query = self::select('articles.*')
            ->join(ArticleColumnsRelation::TABLE_NAME, 'article_id', '=', 'id')
            ->where('status', self::STATUS_RELEASE)
            ->where('type', self::TYPE_ARTICLE)
            ->where('is_top', '=', self::IS_TOP)
            ->orderBy('write_time', 'desc')
            ->orderBy('id', 'desc')
            ->limit($count);

        if (!is_null($columnId)) {
            $query->where('column_id', $columnId);
        }

        return $query->get();
    }

    /**
     * 通过栏目ID获取高PV文章列表
     * @param int|null $columnId 栏目ID，若获取全部文章，传null
     * @param int $count
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getHots($columnId, $count = 3)
    {
        $query = self::select('articles.*')
            ->join(ArticleColumnsRelation::TABLE_NAME, 'article_id', '=', 'id')
            ->where('status', self::STATUS_RELEASE)
            ->where('type', self::TYPE_ARTICLE)
            ->orderBy('pv', 'desc')
            ->orderBy('write_time', 'desc')
            ->orderBy('id', 'desc')
            ->limit($count);

        if (!is_null($columnId)) {
            $query->where('column_id', $columnId);
        }

        return $query->get();
    }

    /**
     * 状态文本
     * @return string
     */
    public function getStatusTextAttribute()
    {
        if (isset($this->attributes['status']) && $this->attributes['status'] == self::STATUS_RELEASE) {
            return '发布';
        } else {
            return '下线';
        }
    }

    /**
     * 类型文本
     * @return string
     */
    public function getTypeTextAttribute()
    {
        switch ($this->attributes['type']) {
            case self::TYPE_ARTICLE:
                return '文章';
            case self::TYPE_PAGE:
                return '页面';
            default:
                return '未知';
        }
    }

}
