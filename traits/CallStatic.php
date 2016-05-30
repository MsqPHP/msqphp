<?php declare(strict_types = 1);
namespace msqphp\traits;

trait CallStatic
{
    /**
     * 万能静态call
     * @param  string $method 方法名
     * @param  array  $args   参数
     * @throws TraitsException
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        static $func = [];
        if (!isset($func[$method])) {
            $framework_path = \msqphp\Environment::getPath('framework');
            $namespace = strtr(substr(__CLASS__, 7, strrpos(__CLASS__, '\\') - 7), '\\', DIRECTORY_SEPARATOR);
            $file = $framework_path . $namespace . DIRECTORY_SEPARATOR.'staticMethods'.DIRECTORY_SEPARATOR.$method.'.php';
            if (!is_file($file)) {
                $file = str_replace(\msqphp\Environment::getPath('library'), $framework_path, $file);
                if (!is_file($file)) {
                    throw new TraitsException(__CLASS__.'类的'.$method.'不存在');
                }
            }
            $func[$method] = require $file;
        }
        return call_user_func_array($func[$method], $args);
    }
}