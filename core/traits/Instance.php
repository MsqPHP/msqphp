<?php declare(strict_types = 1);
namespace msqphp\core\traits;

trait Instance
{
    // 当前对象实例
    private static $instance = null;

    // 私有化构造方法
    private function __construct()
    {
    }

    // 获取实例
    public static function getInstance() : self
    {
        return static::$instance = static::$instance ?? new static();
    }

    // 实例
    public static function unsetInstance() : void
    {
        static::$instance = null;
    }

    // 禁止克隆
    private function __clone()
    {
        throw new TraitsException('单例对象无法克隆');
    }

    // 禁止唤醒
    private function __wakeup()
    {
        throw new TraitsException('单例对象无法唤醒');
    }
}