<?php declare(strict_types = 1);
namespace msqphp\core\controller;

use msqphp\base;
use msqphp\core;

abstract class Controller
{
    public function __get($property)
    {
        static $gets = [];
        if (!isset($gets[$property])) {
            $file = __DIR__ . DIRECTORY_SEPARATOR . 'gets' .DIRECTORY_SEPARATOR . $property . '.php';
            if (!is_file($file)) {
                $file = str_replace(\msqphp\Environment::getPath('framework'), \msqphp\Environment::getPath('library'), $file);
                if (!is_file($property)) {
                    throw new ControllerException($property.'属性不存在');
                }
            }
            $gets[$property] = require $file;
        }
        return $gets[$property];
    }

    function __call(string $method, array $args)
    {
        static $methods = [];
        if (!isset($methods[$method])) {
            $file = __DIR__ . DIRECTORY_SEPARATOR . 'methods' .DIRECTORY_SEPARATOR . $method . '.php';
            if (!is_file($file)) {
                $file = str_replace(\msqphp\Environment::getPath('framework'), \msqphp\Environment::getPath('library'), $file);
                if (!is_file($method)) {
                    throw new ControllerException($method.'方法不存在');
                }
            }
            $methods[$method] = require $file;
        }
        return call_user_func_array($methods[$method], $args);
    }
}