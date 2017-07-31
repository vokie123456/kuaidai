<?php

namespace App\Components;

/**
 * SQL分析
 */
class SqlExplain
{

    /**
     * 通过\DB::getQueryLog()获取执行到当前的所有SQL，并通过EXPLAIN进行分析
     */
    public static function explain()
    {
        $sqls = \DB::getQueryLog();
        $rows = array();

        foreach ($sqls as $sql) {
            $results = \DB::select("explain {$sql['query']}", $sql['bindings']);
            $appends = [];
            foreach ($results as $key => $result) {
                $append = (array)$result;
                if (!self::isProblemSql($append)) {
                    continue;
                }

                $append['query'] = $sql['query'];
                $append['bindings'] = $sql['bindings'];
                $appends[] = $append;
            }

            $rows = array_merge($rows, $appends);
        }

        exit(view('sql_explain', ['rows' => $rows]));
    }

    /**
     * 判断是否为问题SQL
     * @param $explain
     * @return bool
     */
    private static function isProblemSql($explain)
    {
//        system > const > eq_ref > ref > fulltext > ref_or_null > index_merge > unique_subquery > index_subquery > range > index > ALL
        // 全表扫描
        if (strtolower($explain['type']) == 'all') {
            return true;
        }

        // 返回记录过多
        if ($explain['rows'] > 100) {
            return true;
        }

        return false;
    }

}