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
        if (0 === strpos($class_name, 'msqphp\\')) {
            $file = \msqphp\Environment::getPath('framework').str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 7)) . '.php';
            is_file($file) || $file = \msqphp\Environment::getPath('library').str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 7)) . '.php';
            if (is_file($file)) {
                static::include($file);
                return true;
            }
        }


        if (0 === strpos($class_name, 'app\\')) {
            $file = \msqphp\Environment::getPath('application').str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 4)) . '.php';
            if (is_file($file)) {
                static::include($file);
                return true;
            }
        }


        if (0 === strpos($class_name, 'apptest\\')) {
            $file = \msqphp\Environment::getPath('application').str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 4)) . '.php';
            return is_file($file) && static::include($file);
        }
        return false;
    }
    public static function include(string $file)
    {
        $GLOBALS['autoloader_class'][] = $file;
        include $file;
    }
}