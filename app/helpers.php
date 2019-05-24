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

if (!function_exists('object2array')) {
    function object2array($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = object2array($value);
            }
        }
        return $array;
    }
}
