<?php declare(strict_types = 1);
namespace msqphp\core\cli;

use msqphp\Cli;
use msqphp\base\file\File;
use msqphp\core\cron\Cron;

final class CliCron
{
    private static function isRuned() : bool
    {
        return is_file(\msqphp\Environment::getPath('storage').'framework'.DIRECTORY_SEPARATOR.'cron_run');
    }
    public static function run() : void
    {
        if (static::isRuned()) {
            return;
        }
        File::write(\msqphp\Environment::getPath('storage').'framework'.DIRECTORY_SEPARATOR.'cron_run', '');

        Cli::forever(function() {
            Cron::run();
            $next = abs(Cron::getNextRunTime() - time());
            Cli::memory_limit(5*1024*1024, function() {
                File::delete(\msqphp\Environment::getPath('storage').'framework'.DIRECTORY_SEPARATOR.'cron_run');
            });
            sleep($next > 10 ? 10 : $next);
        });
    }
    public static function status() : void
    {

    }
    public static function stop() : void
    {

    }
    public static function rerun() : void
    {
        static::stop();
        static::run();
    }
}