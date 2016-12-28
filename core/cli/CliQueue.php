<?php declare(strict_types = 1);
namespace msqphp\core\cli;

use msqphp\Cli;
use msqphp\base\file\File;
use msqphp\core\queue\Queue;

final class CliQueue
{
    private static function getPath() : string
    {
        return \msqphp\Environment::getPath('storage').'framework'.DIRECTORY_SEPARATOR.'queue_run';
    }
    private static function isRuned() : bool
    {
        return is_file(static::getPath());
    }
    public static function run() : void
    {
        if (static::isRuned()) {
            return;
        }
        File::write(static::getPath(), getpid());

        Cli::forever(function() {
            Queue::run();
            $next = abs(Queue::getNextRunTime() - time());
            Cli::memory_limit(5*1024*1024, function() {
                File::delete(static::getPath());
            });
            sleep($next > 10 ? 10 : $next);
        });
    }
    public static function status() : void
    {

    }
    public static function stop() : void
    {
        Cli::kill((int)File::get(static::getPath()));
    }
    public static function rerun() : void
    {
        static::stop();
        static::run();
    }
}
}