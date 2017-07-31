<?php

namespace App\Models;

use App\Components\CacheName;
use Cache;
use RedisClient;

class Link extends \Eloquent
{

    protected $fillable = [
        'title', 'url'
    ];

    /**
     * 删除
     * @param $id
     * @return int
     */
    public static function del($id)
    {
        /** @var self $model */
        $model = self::findOrFail($id);

        $model->delete();

        return self::destroy($id);
    }

    /**
     * 通过ID更新数据
     * @param $id
     * @param $data
     * @return bool
     */
    public static function updateById($id, $data)
    {
        $model = self::findOrFail($id);

        $result = $model->update($data);

        return $result;
    }

}
