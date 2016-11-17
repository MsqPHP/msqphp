<?php declare(strict_types = 1);
namespace msqphp\core\cron;

use msqphp\base;

trait CronLogTrait
{
    /**
     * 日志写入
     *
     * @param  string $message 信息
     *
     * @return void
     */
    private static function recordLog(string $message) : void
    {
        try {
            base\file\File::append(static::getLogFilePath(), $message . PHP_EOL, true);
        } catch(base\file\FileException $e) {
            throw new CronException('定时任务日志记录出错,错误原因:' . $e->getMessage());
        }
    }
}