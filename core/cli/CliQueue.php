<?php declare(strict_types = 1);
namespace msqphp\core\cli;

use msqphp\Cli;
use msqphp\base\file\File;
use msqphp\core\queue\Queue;

final class CliCron
{
    public static function run() : void
    {
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