<?php declare(strict_types = 1);
namespace Core\Base\Cache\Driver;

use Core\Base;

class Shmop implements Base\Cache\CacheInterface
{
    private $driver = null;
    public function __construct($config)
    {
        $this->$driver = Base\Shmop\Shmop::getInstance();
    }
    public function available(string $key) : bool
    {

    }
    //设置缓存
    /*if (defined('NO_CACHE')) {
            return true;
        }
    */
    public function set(string $key, $value, int $expire = 1800) : bool
    {

    }
    //得到缓存信息
    public function get(string $key)
    {
        
    }
    //清楚缓存
    public function delete(string $key) : bool 
    {

    }
    //清除指定前缀的所有过期的缓存
    public function clear() : bool 
    {

    }
}