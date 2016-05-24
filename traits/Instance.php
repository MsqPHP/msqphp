<?php declare(strict_types = 1);
namespace msqphp\traits;

trait Instance
{
    //当前cookie实例
    private static $instance = null;
    /**
     * 获得当前对象
     * @return self
     */
    public static function getInstance() : self
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }
        return static::$instance;
    }
}