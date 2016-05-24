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
        static $gets = [];
        if (!isset($gets[$property])) {
            $framework_path = \msqphp\Environment::getPath('framework');
            $class = explode('\\', __CLASS__);
            $file = $framework_path . $class[1] . DIRECTORY_SEPARATOR . $class[2] . DIRECTORY_SEPARATOR.'gets'.DIRECTORY_SEPARATOR.$property.'.php';
            if (!is_file($file)) {
                $file = str_replace(\msqphp\Environment::getPath('framework'), \msqphp\Environment::getPath('library'), $file);
                if (!is_file($property)) {
                    throw new TraitsException($property.'属性不存在');
                }
            }
            $gets[$property] = require $file;
        }
        return $gets[$property];
    }
}