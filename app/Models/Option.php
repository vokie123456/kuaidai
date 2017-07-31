<?php

namespace App\Models;

use App\Components\CacheName;
use Cache;
use RedisClient;

/**
 * @property string option_name
 */
class Option extends \Eloquent
{

    protected $fillable = [
        'option_name', 'remark', 'option_value'
    ];

    /**
     * 通过id更新
     * @param $id
     * @param $data
     * @return bool
     */
    public static function updateById($id, $data)
    {
        $model = self::findOrFail($id);

        $result = $model->update($data);

        Cache::forget(self::cacheKey($data['option_name']));

        return $result;
    }

    /**
     * 获取选项
     * @param $optionName
     * @param null $default
     * @return null|string
     */
    public static function getOption($optionName, $default = null)
    {
        $key = self::cacheKey($optionName);

        if (!Cache::has($key)) {
            $options = self::all()->toArray();
            $values = array();

            foreach ($options as $option) {
                $values[self::cacheKey($option['option_name'])] = $option['option_value'];
            }

            Cache::putMany($values, 365 * 24 * 60);
        }

        return Cache::get($key, $default);
    }

    /**
     * 获取缓存Key
     * @param $optionName
     * @return string
     */
    private static function cacheKey($optionName)
    {
        return CacheName::OPTIONS[0] . $optionName;
    }
}
