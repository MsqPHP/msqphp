<?php declare(strict_types = 1);
namespace msqphp\base;

trait Base
{
    /**
     * 万能静态call
     * @param  string $method 方法名
     * @param  array  $args   参数
     * @throws BaseException
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        static $func = [];
        if (!isset($func[$method])) {
            $file = __DIR__.DIRECTORY_SEPARATOR.explode('\\', __CLASS__)[3].DIRECTORY_SEPARATOR.'staticMethods'.DIRECTORY_SEPARATOR.$method.'.php';
            if (!is_file($file)) {
                $file = str_replace(\msqphp\Environment::getPath('library'), \msqphp\Environment::getPath('framework'), $file);
                if (!is_file($file)) {
                    throw new BaseException(__CLASS__.$method.'不存在');
                }
            }
            $func[$method] = require $file;
        }
        return call_user_func_array($func[$method], $args);
    }
}