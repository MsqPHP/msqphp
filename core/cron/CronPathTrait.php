<?php declare(strict_types = 1);
namespace msqphp\core\cron;

trait CronPathTrait
{
    // 得到定时任务文件目录
    private static function getPath() : string
    {
        static $path = '';
        return $path = $path ?: \msqphp\Environment::getPath('storage') . 'framework' . DIRECTORY_SEPARATOR . 'cron' . DIRECTORY_SEPARATOR;
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
}