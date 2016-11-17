<?php declare(strict_types = 1);
namespace msqphp\core\loader;

trait BaseTrait
{
    // 所有加载过的文件列表
    private static $loadedClasses = [];

    // 当前智能加载文件列表
    private static $classes = [];

    // 清空当前智能加载文件列表
    public static function emptyClasses() : void
    {
        static::$classes = [];
    }

    // 添加一个文件到当前智能加载文件列表
    public static function addClasses(string $file_path) : void
    {
        static::$loadedClasses[] = static::$classes[] = $file_path;
    }

    // 获取当前智能加载文件列表
    public static function getClasses() : array
    {
        return static::$classes;
    }

    // 获取所有加载过的文件
    public static function getLoadedClasses() : array
    {
        return static::$loadedClasses;
    }
}