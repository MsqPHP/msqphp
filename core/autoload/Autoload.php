<?php declare(strict_types = 1);
namespace msqphp\core\autoload;

final class Autoload
{
    public static function register()
    {
        spl_autoload_register('\msqphp\core\autoload\Autoload::handler', false, true);
    }
    public static function unregister()
    {
        spl_autoload_unregister('\msqphp\core\autoload\Autoload::handler');
    }
    public static function handler(string $class_name) : bool
    {
        if (0 === strpos($class_name, 'msqphp//')) {
            $file = \msqphp\Environment::getPath('framework').str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 8));
            is_file($file) || $file = \msqphp\Environment::getPath('library').str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 8));
            return static::include($file);
        }
        if (0 === strpos($class_name, 'app//')) {
            $file = \msqphp\Environment::getPath('application').str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 8));
            return static::include($file);
        }
        if (0 === strpos($class_name, 'apptest//')) {
            return static::include($file);
        }
        return false;
    }
    public static function include(string $file) : bool
    {
        if (!is_file($file)) {
            return false;
        }
        $GLOBALS['autoloader_class'][] = $file;
        include $file;
        return true;
    }
}