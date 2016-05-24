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
            $framework_path = \msqphp\Environment::getPath('framework');
            $class = explode('\\', __CLASS__);
            $file = $framework_path . $class[1] . DIRECTORY_SEPARATOR . $class[2] . DIRECTORY_SEPARATOR.'methods'.DIRECTORY_SEPARATOR.$method.'.php';
            if (!is_file($file)) {
                $file = str_replace(\msqphp\Environment::getPath('framework'), $framework_path, $file);
                if (!is_file($method)) {
                    throw new TraitsException($method.'方法不存在');
                }
            }
            $methods[$method] = require $file;
        }
        return call_user_func_array($methods[$method], $args);
    }
}