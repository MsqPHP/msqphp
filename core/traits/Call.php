<?php declare(strict_types = 1);
namespace msqphp\core\traits;

trait Call
{
    /**
     * 万能call
     *
     * @param  string $method 方法名
     * @param  array  $args   参数
     *
     * @throws TraitsException
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        // 所有方法
        static $methods = [];

        if (!isset($methods[$method])) {

            $framework_path = \msqphp\Environment::getPath('framework');

            // 去类命名空间头msqphp和类名
            // 例:msqphp\base\dir\Dir ----> base\dir
            $namespace = str_replace([strrchr(__CLASS__, '\\'), 'msqphp\\'], '', __CLASS__);

            // 拼接文件路径
            $file_path = $framework_path . strtr($namespace, '\\', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR .'methods' . DIRECTORY_SEPARATOR . $method . '.php';

            // 不存在,则检测扩展目录是否存在
            is_file($file_path) || $file_path = str_replace($framework_path, \msqphp\Environment::getPath('library'), $file_path);

            if (!is_file($file_path)) {
                throw new TraitsException(__CLASS__.'类的扩展方法'.$method.'不存在');
            }

            // 添加方法
            $methods[$method] = require $file_path;
        }

        return call_user_func_array($methods[$method], $args);
    }
}