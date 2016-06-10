<?php declare(strict_types = 1);
namespace msqphp\traits;

trait Call
{

    /**
     * 万能静态call
     * @param  string $method 方法名
     * @param  array  $args   参数
     * @throws BaseException
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        static $methods = [];
        if (!isset($methods[$method])) {

            $framework_path = dirname(__DIR__) . DIRECTORY_SEPARATOR;

            $namespace = strtr(str_replace([strrchr(__CLASS__, '\\'),'msqphp\\'],'',__CLASS__), '\\', DIRECTORY_SEPARATOR);

            $file = $framework_path . $namespace . DIRECTORY_SEPARATOR.'methods'.DIRECTORY_SEPARATOR.$method.'.php';

            !is_file($file) && $file = str_replace($framework_path, \msqphp\Environment::getPath('library'), $file);

            if (!is_file($file)) {
                throw new TraitsException(__CLASS__.'类的'.$method.'方法不存在');
            }

            $methods[$method] = require $file;
        }
        return call_user_func_array($methods[$method], $args);
    }
}