<?php declare(strict_types = 1);
namespace msqphp\core\traits;

trait Get
{
    /**
     * 万能get
     *
     * @param  string $property 属性
     *
     * @throws TraitsException
     * @return miexd
     */
    public function __get(string $property)
    {
        // 所有方法
        static $gets = [];

        if (!isset($gets[$property])) {

            $framework_path = \msqphp\Environment::getPath('framework');

            // 去类命名空间头msqphp和类名
            // 例:msqphp\base\dir\Dir ----> base\dir
            $namespace = str_replace([strrchr(__CLASS__, '\\'), 'msqphp\\'], '', __CLASS__);

            // 拼接文件路径
            $file_path = $framework_path . strtr($namespace, '\\', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR .'gets' . DIRECTORY_SEPARATOR . $property . '.php';

            // 不存在,则检测扩展目录是否存在
            is_file($file_path) || $file_path = str_replace($framework_path, \msqphp\Environment::getPath('library'), $file_path);

            if (!is_file($file_path)) {
                throw new TraitsException(__CLASS__.'类的'.$property.'属性不存在');
            }

            // 添加方法
            $gets[$property] = require $file_path;
        }

        return $gets[$property];
    }
}