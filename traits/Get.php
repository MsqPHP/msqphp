<?php declare(strict_types = 1);
namespace msqphp\traits;

trait Get
{
    /**
     * 万能get
     *
     * @param  string $property 属性
     *
     * @return miexd
     */
    public function __get(string $property)
    {
        //get集合
        static $gets = [];

        //如果不存在
        if (!isset($gets[$property])) {

            //框架路径
            $framework_path = dirname(__DIR__) . DIRECTORY_SEPARATOR;

            //命名空间转换为目录 msqphp\base\dir\Dir ----> base\dir
            $namespace = strtr(str_replace([strrchr(__CLASS__, '\\'),'msqphp\\'],'',__CLASS__), '\\', DIRECTORY_SEPARATOR);

            //拼装路径
            $file = $framework_path . $namespace . DIRECTORY_SEPARATOR . 'gets' . DIRECTORY_SEPARATOR . $property . '.php';

            //不存在则替换
            is_file($file) || $file = str_replace($framework_path, \msqphp\Environment::getPath('library'), $file);

            //存在载入否则报错
            if (is_file($file)) {
                $gets[$property] = require $file;
            } else {
                throw new TraitsException(__CLASS__.'类的'.$property.'不存在');
            }

        }

        return $gets[$property];
    }
}