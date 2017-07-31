<?php

namespace App\Components;

use App\Models\Option;

class CacheName
{
    const OPTIONS = [
        'options:',
        '选项配置'
    ];

    const PAGE_HOME = [
        'page_home',
        '页面-首页'
    ];

    const PAGE_ARTICLE = [
        'page_article',
        '页面-文章详情'
    ];

    const PAGE_COLUMN = [
        'page_column',
        '页面-栏目详情'
    ];

    const PAGE_TAG = [
        'page_tag',
        '页面-标签页'
    ];

    const STAT_PV = [
        'stat_pv',
        '统计-PV'
    ];

    const LOGIN_CAPTCHA = [
        'login_captcha',
        '注册验证码'
    ];

    /**
     * 获取所有缓存key
     */
    public static function getAllKey()
    {
        $ref = new \ReflectionClass(self::class);

        return $ref->getConstants();
    }

    /**
     * 清除缓存key
     * @param array $keys
     */
    public static function clear(array $keys)
    {
        foreach ($keys as $key) {
            $func = 'clear' . trim(studly_case($key), ':');
            if (method_exists(self::class, $func)) {
                self::$func($key);
            } else {
                \Cache::forget($key);
            }
        }
    }

    /**
     * 清除option:缓存
     * @param $keyPrefix
     */
    protected static function clearOptions($keyPrefix)
    {
        $options = Option::get()->toArray();
        $options = array_column($options, 'option_name');

        foreach ($options as $option) {
            $key = "{$keyPrefix}{$option}";
            \Cache::forget($key);
        }
    }

}