<?php

namespace App\Tools;

class Timezone
{
    static $timezone = null;

    public static function timezone()
    {
        if (empty(static::$timezone)) {
            if (\Auth::check()) {
                static::$timezone = \Auth::user()->meta('timezone');
            }

            if (empty(static::$timezone)) {
                static::$timezone = config('qc.timezone');
            }
        }

        return static::$timezone;
    }
}
