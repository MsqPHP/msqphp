<?php declare(strict_types = 1);
namespace msqphp\traits;

trait Instance
{
    //当前对象实例
    protected static $instance = null;
    /**
     * 获得当前对象
     *
     * @return self
     */
    public static function getInstance() : self
    {
        if (null === static::$instance) {
            return static::$instance = new self();
        } else {
            return static::$instance;
        }
    }
    public static function unsetInstance()
    {
        static::$instance = null;
    }
    private function __clone()
    {
        throw new TraitsException('单例对象无法克隆');
    }
    private function __wakeup()
    {
        throw new TraitsException('单例对象无法wakeup');
    }
}