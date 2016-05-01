<?php declare(strict_types = 1);
namespace Msqphp\Base\Cache\Driver;

use Msqphp\Base;
use Msqphp\Core;

class Memcached implements Core\Cache\CacheInterface
{
    private $handler = null;
    private $vaule = '';
    //构造函数
    public function __construct(array $config)
    {
        $this->handler = Base\Memcached\Memcached::getInstance();
    }
    /**
     * @param  array  $config 缓存驱动配置
     * @param  string $key    缓存键
     * @param  string $val    缓存值
     * @param  int    $expire 缓存有效期
     * @return bool 是否成功 | 是否存在
     */
    //是否可用
    public function available(string $key) : bool
    {
        if (defined('NO_CACHE')) {
            return false;
        }
        if (!$value = $this->handler->get($key)) {
            return false;
        }
        $this->value = $value;
        return true;
    }
    //设置缓存
    public function set(string $key, $value, int $expire = 1800) : bool
    {
        if (defined('NO_CACHE')) {
            return true;
        }
        return $this->handler->set($key,$value,$expire);
    }
    //得到缓存信息
    public function get(string $key)
    {
        if ($this->value !== '') {
            $value = $this->value;
            $this->value = '';
            return $value;
        }
        return $this->handler->get($key);
    }
    //清楚缓存
    public function delete(string $key) : bool 
    {
        return $this->handler->delete($key);
    }
    //清除指定前缀的所有过期的缓存
    public function clear() : bool 
    {
        return true;
    }
}