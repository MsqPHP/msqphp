<?php declare(strict_types = 1);
namespace msqphp\core\cli;

use msqphp\base;

class Cli
{
    private $args = [];
    public static function init()
    {
        static::$args = $GLOBALS['argv'];
    }
    public static function run()
    {
        $args = static::$args;
        if ('install' === $args[1]) {
            \msqphp\Framework::install(dirname($args[0]));
        } elseif ('update' === $args[1]) {
            \msqphp\Framework::update(dirname($args[0]));
        }
        return;
    }
}