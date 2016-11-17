<?php declare(strict_types = 1);
namespace msqphp\core\cron;

use msqphp\base;
use msqphp\core\traits;

final class Cron
{
    use CronLogTrait;
    use CronPathTrait;
    use CronMethodTrait;

    private static $info = null;
    private static $next_time = -1;
    private static $lock = false;

    /**
     * 添加任务
     *
     * @param string $name  任务名称
     * @param string $type  任务类型
     * @param string $value 任务值
     * @param int    $time  执行时间
     *
     * @return void
     */
    public static function add(string $name, string $type, string $value, int $time) : void
    {
        base\file\File::append(static::getCacheFilePath(), '[' . $name . '](' . $type . '@#' . $time . '){' . $value . '}');

        $time < static::getNextRunTime() && static::tidy();
    }

    // 执行定时任务
    public static function run() : void
    {
        $now = time();

        // 现在时间大于等于下一次执行时间,则运行
        if ($now >= static::getNextRunTime()) {
            // 获取任务信息
            $info = static::getInfo();
            // show($info);exit;
            if (false === static::$lock) {
                // 当存在任务且任务执行时间大于当前时间,则运行任务
                while (isset($info[0]) && $now >= $info[0]['time']) {
                    // 调用方法并移除
                    static::runMethod(array_shift($info));
                }
                static::$info = $info;
                static::tidy();
            } else {
                static::recordLog(date('Y-m-d H:i:s').'文件锁入,跳过运行');
            }
        }
        static::recordLog(date('Y-m-d H:i:s').'运行过一次定时任务');
    }

    // 整理定时任务
    public static function tidy() : void
    {
        $info = static::getInfo();
        if (false === static::$lock) {
            // 获取合并后的信息
            static::$info      = $info      = static::merage($info, static::sort(static::getCacheInfo()));
            // 获取下次运行时间
            static::$next_time = $next_time = isset($info[0]) ? $info[0]['time'] : 0;
            // 清空缓存并写入
            base\file\File::write(static::getInfoFilePath(), serialize($info));
            base\file\File::write(static::getNextRunTimeFilePath(), $next_time);
            base\file\File::empty(static::getCacheFilePath());
        } else {
            static::recordLog(date('Y-m-d H:i:s').'文件锁入,跳过运行');
        }
    }
    private static function merage(array $info, array $append) : array
    {
        $result = [];
        while (isset($info[0]) && isset($append[0])) {
            $result[] = $info[0]['time'] <= $append[0]['time'] ? array_shift($info) : array_shift($append);
        }
        return array_merge($result, $info, $append);
    }
    // 整理定时任务信息
    private static function sort(array $arr) : array
    {
        // 数组长度
        $l = count($arr);
        if ($l <= 1) {
            return $arr;
        }
        $mid   = $arr[0];
        $left  = [];
        $right = [];
        for (--$l; $l > 0;--$l) {
            $mid['time'] > $arr[$l]['time'] && ($left[]=$arr[$l]) || ($right[]=$arr[$l]);
        }
        return array_merge(static::sort($left), [$mid], static::sort($right));
    }

    // 获取数据
    private static function getInfo() : array
    {
        if (null !== static::$info) {
            return static::$info;
        }
        $info_file = static::getInfoFilePath();
        $info = [];
        if (is_file($info_file)) {
            $resource = fopen($info_file, 'r+');
            if (flock($resource, LOCK_EX)) {
                $result = fread($resource, filesize($info_file) + 1);
                $info = $result === '' ? [] : unserialize($result);
                flock($resource, LOCK_UN);
                fclose($resource);
            } else {
                static::$lock = true;
            }
        }
        return static::$info = $info;
    }
    // 得到下一次运行时间
    private static function getNextRunTime() : int
    {
        // 如果载入过
        if (static::$next_time !== -1) {
            // 返回
            return static::$next_time;
        } else {
            // 文件存在则载入,否则取0
            $next_file = static::getNextRunTimeFilePath();
            return static::$next_time = is_file($next_file) ? (int) base\file\File::get($next_file) : time();
        }
    }
    // 获取缓存信息
    private static function getCacheInfo() : array
    {
        $cache_file = static::getCacheFilePath();

        if (!is_file($cache_file)) {
            return [];
        }

        $cache = base\file\File::get($cache_file);

        $info = [];

        // 例: [删除文件](deleteFile@#1245454545){D:\msqphp\a.txt}
        // []之间为任务名,()中@#前为任务类型,后面为执行时间,{}中为值
        while (isset($cache[0]) && $cache[0] === '[') {
            if (false !== $pos = strpos($cache, ']')) {
                $name = substr($cache, 1 , $pos - 1);
                if (false !== $pos_next = strpos($cache, ')')) {
                    [$type, $time] = explode('@#', substr($cache, $pos + 2, $pos_next - $pos - 2));
                    if (false !== $pos_last = strpos($cache, '}')) {
                        $value = substr($cache, $pos_next + 2, -1);
                        $info[] = ['name'  => $name, 'value' => $value, 'time'  => (int) $time, 'type'  => $type];
                        $cache = substr($cache, $pos_last + 1);
                        continue;
                    }
                }
            }
            throw new CronException('错误的cron缓存格式,解析失败');
        }
        return $info;
    }
}