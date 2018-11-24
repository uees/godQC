<?php

if (!function_exists('timerStop')) {
    /**
     * Returns the time it took LARAVEL_START
     *
     * @param int $precision
     * @return string
     */
    function timerStop($precision = 3)
    {
        defined('LARAVEL_START') or abort(500);
        $time_end = microtime(true);
        $time_total = $time_end - LARAVEL_START;
        return number_format($time_total, $precision);
    }
}

if (!function_exists('queryCondition')) {
    /**
     * @param string $filed
     * @param string $query_string
     * @return array
     */
    function queryCondition($filed, $query_string)
    {
        $keys = explode(' ', $query_string);

        $condition = [];
        foreach ($keys as $key) {
            if (!empty($key)) {
                array_push($condition, [$filed, 'like', "%{$key}%"]);
            }
        }

        return $condition;
    }
}

if (!function_exists('make_excerpt')) {
    // 有个特殊的地方就是截取的字数只能是3的倍数，不然有时候会出现某个字被截了一半的情况。
    function make_excerpt($content, $count = 3 * 80)
    {
        $content = preg_replace("@<script(.*?)</script>@is", "", $content);
        $content = preg_replace("@<iframe(.*?)</iframe>@is", "", $content);
        $content = preg_replace("@<style(.*?)</style>@is", "", $content);
        // $content = preg_replace("@<(.*?)>@is", "", $content);  // this is strip_tags
        $content = strip_tags(stripslashes($content));  // 去掉转义和html标签
        $content = str_replace(PHP_EOL, '', $content);  // 去掉换行符
        $content = trim($content);  // 去掉两边多余的空格

        $content = preg_replace('/\s{2,}/', ' ', $content);  // 将连续的空白替换为一个

        $res = mb_substr($content, 0, $count, 'UTF-8');  // 截取固定的长度

        if (mb_strlen($content, 'UTF-8') > $count) {
            $res = $res . "...";
        }

        return $res;
    }
}
