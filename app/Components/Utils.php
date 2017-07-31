<?php

namespace App\Components;

/**
 * 工具包
 */
class Utils
{

    /**
     * 替换字符串，并指定次数
     * @param $search
     * @param $replace
     * @param $subject
     * @param int $limit
     * @return mixed
     */
    public static function strReplaceLimit($search, $replace, $subject, $limit = -1)
    {
        if (is_array($search)) {
            foreach ($search as $k => $v) {
                $search[$k] = '`' . preg_quote($search[$k], '`') . '`';
            }
        } else {
            $search = '`' . preg_quote($search, '`') . '`';
        }
        return preg_replace($search, $replace, $subject, $limit);
    }

}