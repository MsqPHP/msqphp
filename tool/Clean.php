<?php declare(strict_types = 1);
namespace msqphp\tool;

use msqphp\base;

final class Clean
{
    public static function all() : void
    {
        static::cache();
        static::view();
        static::session();
        static::log();
        static::framework();
    }
    public static function cache() : void
    {
        app()->cache->clean();
    }
    public static function view() : void
    {
        base\dir\Dir::empty(app()->config->get('view.tpl_part_path'));
        base\dir\Dir::empty(app()->config->get('view.tpl_package_path'));
    }
    public static function session() : void
    {
        app()->session->clean();
    }
    public static function log() : void
    {
        app()->log->clean();
    }
    public static function framework() : void
    {
        base\dir\Dir::empty(\msqphp\Environment::getPath('storage').'framework');
    }
}