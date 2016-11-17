<?php declare(strict_types = 1);
namespace msqphp\core\loader;

trait AutoloadTrait
{
    // 注册自动加载
    public static function register() : void
    {
        spl_autoload_register('\msqphp\core\loader\Loader::handler', false, true);
    }

    // 取消已注册过的自动加载
    public static function unregister() : void
    {
        spl_autoload_unregister('\msqphp\core\loader\Loader::handler');
    }

    // 自动加载函数
    public static function handler(string $class_name) : bool
    {
        // 类的顶级命名空间为
        switch (strstr($class_name, '\\', true)) {
            case 'msqphp' :
                // 框架中是否存在
                $file = \msqphp\Environment::getPath('framework').str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 7)) . '.php';
                // 是否为用户自定义扩展
                is_file($file) || $file = \msqphp\Environment::getPath('library').str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 7)) . '.php';
                // 存在包含,否则false
                return static::include($file);
            case 'app' :
                // 用户应用文件
                $file = \msqphp\Environment::getPath('application').str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 4)) . '.php';
                // 存在包含,否则false
                return static::include($file);
            case 'test' :
                // 用户测试类文件
                $file = \msqphp\Environment::getPath('test').str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 4)) . '.php';
                // 存在包含,否则false
                return static::include($file);
            default :
                return false;
        }
    }

    // 加载文件函数,存在加载,并记录智能加载文件列表中,否侧false
    private static function include(string $file) : bool
    {
        if (is_file($file)) {
            static::addClasses($file);
            include $file;
            return true;
        } else {
            return false;
        }
    }
}