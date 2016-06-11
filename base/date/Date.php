<?php declare(strict_types = 1);
namespace msqphp\base\date;

use msqphp\base;
use msqphp\traits;

final class Date
{
    use traits\CallStatic;
    /**
     * 年月日合法检测
     * @param  int    $year  年
     * @param  int    $month 月
     * @param  int    $day   日
     * @return bool     是否合法
     */
    public static function checkDate(int $year, int $month, int $day) : bool
    {
        return checkdate($month, $day, $year);
    }
    /**
     * 两个时间差的天数
     * @param  int    $year_a       年
     * @param  int    $month_a      月
     * @param  int    $day_a        日
     * @param  int    $year_b       年
     * @param  int    $month_b      月
     * @param  int    $day_c        日
     * @return int    经过天数
     */
    public static function passDay(int $year_a, int $month_a, int $day_a, int $year_b, int $month_b, int $day_b) : int
    {
        $time_a =strtotime($year_a.'-'.$month_a.'-'.$day_a);
        $time_b =strtotime($year_b.'-'.$month_b.'-'.$day_b);
        return round((abs($time_a-$time_b)) / 86400);
    }
    /**
     * 格式化时间
     */
    public static function format(int $time, string $type = '')
    {
        switch ($type) {
            case 'zh-cn':
                return date('Y-m-d h:i', $time);
            case 'en-us':
                return date('Y-d-m h:i', $time);
            case 'en-uk':
                return date('d-m-Y h:i', $time);
            default:
                return date('Y-m-d h:i', $time);
        }
    }
    public static function today() : int
    {
        return strtotime(date('Y-m-d 00:00:00'));
    }
    public static function before(int $time) : string
    {
        $today = static::today();
        if ($time > $today) {
            $limit = time() - $time;
            if ($limit < 60 ) {
                $result = '刚刚';
            } elseif ($limit < 3600) {
                $result = floor($limit / 60) . ' 分钟前';
            } elseif ($limit < 86400) {
                $result = floor($limit / 3600) . ' 小时前';
            }
        } else {
            if ($time > ($today - 86400)) {
                $result = ' 昨天 ' . date('H:i', $time);
            } elseif ($time > ($today - 172800)) {
                $result = ' 前天 ' . date('H:i', $time);
            } else {
                $result = date('Y-m-d', $time);
            }
        }
        return $result;
    }
}