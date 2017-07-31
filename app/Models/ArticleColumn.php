<?php

namespace App\Models;

use App\Components\CacheName;
use Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property int type
 * @property string column_name
 */
class ArticleColumn extends \Eloquent
{
    use SoftDeletes;

    /** 显示 */
    const IS_SHOW_TRUE = 1;

    /** 不显示 */
    const IS_SHOW_FALSE = 2;

    /** 类型-列表 */
    const TYPE_LIST = 1;

    /** 类型-页面 */
    const TYPE_PAGE = 2;

    /** 类型-视图 */
    const TYPE_VIEW = 3;

    protected $fillable = [
        'column_name', 'type', 'alias', 'weight', 'is_show', 'parent_id', 'view'
    ];

    protected $appends = [
        'is_show_text',
        'type_text'
    ];

    /**
     * 首页栏目
     * @return array
     */
    public static function homeColumns()
    {
        return self::where('is_show', self::IS_SHOW_TRUE)
            ->orderBy('weight', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * 通过别名获取
     * @param $alias
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public static function findByAliasOrFail($alias)
    {
        return self::where('alias', $alias)
            ->firstOrFail();
    }

    /**
     * 创建
     * @param $data
     * @return static
     */
    public static function store($data)
    {
        $model = ArticleColumn::create($data);

        return $model;
    }

    /**
     * 通过id更新
     * @param $id
     * @param $data
     * @return bool
     */
    public static function updateById($id, $data)
    {
        $model = ArticleColumn::findOrFail($id);

        $result = $model->update($data);

        return $result;
    }

    /**
     * 通过id删除
     * @param $id
     * @return bool|null
     */
    public static function deleteById($id)
    {
        $model = ArticleColumn::findOrFail($id);

        $result = $model->delete();

        return $result;
    }

    /**
     * 是否显示文本
     * @return string
     */
    public function getIsShowTextAttribute()
    {
        if (isset($this->attributes['is_show']) && $this->attributes['is_show'] == self::IS_SHOW_TRUE) {
            return '是';
        } else {
            return '否';
        }
    }

    public function getTypeTextAttribute()
    {
        if (isset($this->attributes['type'])) {
            switch ($this->attributes['type']) {
                case self::TYPE_PAGE:
                    return '页面';
                case self::TYPE_LIST:
                    return '列表';
                case self::TYPE_VIEW:
                    return '视图';
            }
        }

        return '列表';
    }
}
