<?php declare(strict_types = 1);
namespace msqphp\core\cli;

use msqphp\Cli;
use msqphp\base\file\File;
use msqphp\core\cron\Cron;

final class CliCron
{
    private static function isRuned() : bool
    {
        if (!is_file(Cron::getFilePath('pid'))) {
            return false;
        } else {
            return File::get(Cron::getFilePath('pid')) === 'true';
        }
    }
    public static function run() : void
    {
        if (static::isRuned()) {
            return;
        }
        File::write(Cron::getFilePath('pid'), 'true');

        Cron::update(time(), 3600);

        Cli::forever(function() {
            if (!static::isRuned()) {
                return false;
            }
            Cron::run();
            $next = abs(Cron::getNextRunTime() - time());
            Cli::memory_limit(5*1024*1024, function() {
                File::delete(Cron::getFilePath('pid'));
            });
            sleep($next > 10 ? 10 : $next);
            return true;
        });
    }
    public static function status() : void
    {

    }
    public static function stop() : void
    {
        File::delete(Cron::getFilePath('pid'));
    }
    public static function rerun() : void
    {
        static::stop();
        static::run();
    }
}