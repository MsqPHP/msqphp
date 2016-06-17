<?php declare(strict_types = 1);
namespace msqphp\traits;

trait Instance
{
    //当前对象实例
    private static $instance = null;
    /**
     * 获得当前对象
     *
     * @return self
     */
    public static function getInstance() : self
    {
        return static::$instance = static::$instance ?? new static();
    }
    /**
     * 销毁当前对象实例
     *
     * @return void
     */
    public static function unsetInstance()
    {
        static::$instance = null;
    }
    /**
     * 禁止克隆
     *
     * @throws TraitsException
     * @return void
     */
    private function __clone()
    {
        throw new TraitsException('单例对象无法克隆');
    }
    /**
     * 禁止唤醒
     *
     * @throws TraitsException
     * @return void
     */
    private function __wakeup()
    {
        throw new TraitsException('单例对象无法wakeup');
    }
}