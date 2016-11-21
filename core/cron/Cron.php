<?php declare(strict_types = 1);
namespace msqphp\core\cron;

use msqphp\base;
use msqphp\core\traits;

final class Cron
{
    private static $lock_handler = null;
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
        base\file\File::append(static::getCacheFilePath(), $time . '[' . $name . '](' . $type . '@' . $value . ')' . PHP_EOL, true);
    }

    // 执行定时任务
    public static function run() : void
    {
        $now = time();

        // 现在时间大于等于下一次执行时间,则运行
        if ($now >= static::getNextRunTime()) {
            // show($info);exit;
            if (false === static::isLocked()) {
                // 获取任务信息
                $info_file = static::getInfoFilePath();
                $cache_file = static::getCacheFilePath();
                $cache_use_file = static::getCacheUseFilePath();
                is_file($cache_file) && base\file\File::rename($cache_file, $cache_use_file);
                $info = [];
                if (is_file($info_file)) {
                    $fp = fopen($info_file, 'r');
                    while (false !== $cron_one = fgets($fp)) {
                        $cron_one = static::readInfo($cron_one);
                        if ($cron_one['time'] <= $now) {
                            // 调用方法并移除
                            CronMethod::runMethod($cron_one);
                        } else {
                            break;
                        }
                    }
                    while (false !== $cron_one = fgets($fp)) {
                        $info[] = static::readInfo($cron_one);
                    }
                    fclose($fp);
                }
                $cache=[];
                if (is_file($cache_use_file)) {
                    $fp = fopen($cache_use_file, 'r');
                    while (false !== $cron_one = fgets($fp)) {
                        $cron_one = static::readInfo($cron_one);
                        if ($cron_one['time'] <= $now) {
                            // 调用方法并移除
                            CronMethod::runMethod($cron_one);
                        } else {
                            $cache[] = $cron_one;
                        }
                    }
                    fclose($fp);
                    base\file\File::delete($cache_use_file);
                }
                $content = static::toString(static::merage($info, static::sort($cache)));
                // 清空缓存并写入
                base\file\File::write($info_file, $content);
                // 获取下次运行时间
                $next_time = (int) (isset($content[0]) ? substr($content, strpos($content, '[') -1) : $now + 3600);
                $next_time < static::getNextRunTime() && base\file\File::write(static::getNextRunTimeFilePath(), $next_time);
                static::unlocked();
            } else {
                static::recordLog(date('Y-m-d H:i:s').'文件锁入,跳过运行');
            }
            static::recordLog(date('Y-m-d H:i:s').'运行过一次定时任务');
        }
    }
    private static function toString(array $info) : string
    {
        $result = '';
        for ($i = 0,$l = count($info); $i < $l; ++$i) {
            $result.= $info[$i]['time'] . '[' . $info[$i]['name'] . '](' . $info[$i]['type'] . '@' . $info[$i]['value'] . ')' . PHP_EOL;
        }
        return $result;
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

    private static function isLocked() : bool
    {
        if (static::$lock_handler === null) {
            $resource = fopen(static::getLockFilePath(), 'w');
            if (flock($resource, LOCK_EX)) {
                static::$lock_handler = $resource;
                return false;
            } else {
                fclose($resource);
                return true;
            }
        } else {
            return false;
        }
    }
    private static function unlocked() : void
    {
        fclose(static::$lock_handler);
    }
    // 得到下一次运行时间
    private static function getNextRunTime() : int
    {
        $next_file = static::getNextRunTimeFilePath();
        return is_file($next_file) ? (int) base\file\File::get($next_file) : time();
    }

    private static function readInfo(string $text) : array
    {
        $info = [];
        [$left, $right] = explode('](', $text, 2);
        [$time, $name] = explode('[', $left, 2);
        [$type, $value] = explode('@', $right, 2);
        $value = substr($value, 0, -1);
        return ['time'=>$time,'name'=>$name,'value'=>$value,'type'=>$type];
    }

    private static $path = '';
    // 得到定时任务文件目录
    private static function getPath() : string
    {
        if (static::$path === '') {
            static::$path = \msqphp\Environment::getPath('storage') . 'framework' . DIRECTORY_SEPARATOR . 'cron' . DIRECTORY_SEPARATOR;
        }
        return static::$path;
    }

    // 得到定时任务信息文件路径
    private static function getInfoFilePath() : string
    {
        return static::getPath() . 'cron.php';
    }

    // 得到定时任务日志文件路径
    private static function getLogFilePath() : string
    {
        return static::getPath() . date('Ymd') . DIRECTORY_SEPARATOR . 'cron_log.txt';
    }

    // 得到定时任务下次运行时间文件路径
    private static function getNextRunTimeFilePath() : string
    {
        return static::getPath() . 'cron_next_time.txt';
    }
    // 得到定时任务缓存文件路径
    private static function getCacheFilePath() : string
    {
        return static::getPath() . 'cron_cache.txt';
    }
     // 得到定时任务缓存文件路径
    private static function getCacheUseFilePath() : string
    {
        return static::getPath() . 'cron_cache_use.txt';
    }
    // lock文件
    private static function getLockFilePath() : string
    {
        return static::getPath() . 'cron.lock';
    }

    /**
     * 日志写入
     *
     * @param  string $message 信息
     *
     * @return void
     */
    public static function recordLog(string $message) : void
    {
        try {
            base\file\File::append(static::getLogFilePath(), $message . PHP_EOL, true);
        } catch(base\file\FileException $e) {
            throw new CronException('定时任务日志记录出错,错误原因:' . $e->getMessage());
        }
    }
}