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

            $framework_path = dirname(__DIR__) . DIRECTORY_SEPARATOR;

            $namespace = strtr(str_replace([strrchr(__CLASS__, '\\'),'msqphp\\'],'',__CLASS__), '\\', DIRECTORY_SEPARATOR);

            $file = $framework_path . $namespace . DIRECTORY_SEPARATOR.'staticMethods'.DIRECTORY_SEPARATOR.$method.'.php';

            !is_file($file) && $file = str_replace($framework_path, \msqphp\Environment::getPath('library'), $file);

            if (!is_file($file)) {
                throw new TraitsException(__CLASS__.'类的'.$method.'静态方法不存在');
            }

            $func[$method] = require $file;
        }

        return call_user_func_array($func[$method], $args);
    }
}