<?php declare(strict_types = 1);
namespace Tool;
class TimeTool {
    private static $timeZone;
    public static function setTimeZone($timeZone) {
        static::$timeZone = $timeZone;
        date_default_timezone_set($timeZone);
    }
    public static function getTimeZone() {
        static::$timeZone = date_timezone_get();
    }
    public static function getTime() {
        empty($timeZone) && static::getTimeZone();
        return date(static::getTimeFormat());
    }
    public static function getTimeFormat() {
        empty($timeZone) && static::getTimeZone();
        return 'Y-m-d H:i:s';
    }
}