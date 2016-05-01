<?php declare(strict_types = 1);
namespace Msqphp\Base\Memcached;

use Msqphp\Base;

class Memcached
{
    private $memcached = null;
    private static $instance = null;
    private function __construct()
    {
        //载入配置文件
        $config = require \Msqphp\Environment::$config_path.'memcached.php';
        if (!extension_loaded('memcached')) {
            throw new MemcachedException('not support memcached');
        }
        //获得实例
        $this->memcached = $memcached = new \Memcached('Msqphp');
        //是否是原始的
        if ($memcached->isPristine()) {
            //参数设置
            $memcached->setOptions($config['options']);
            $memcached->addServer($config['server'],$config['port']);
            if ($config['multi']) {
                $memcached->addServers($config['servers']);
            }
        }
    }
    public function getMemcached()
    {
        return $this->memcached;
    }
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new Memcached();
        }
        return static::$instance;
    }
    public function add(string $key,$value,int $expire) : bool
    {
        return $this->memcached->add($key,$value,$expire);
    }
    public function get(string $key)
    {
        return $this->memcached->get($key);
    }
    public function append(string $key,$value) : bool
    {
        return $this->memcached->append($key,$value);
    }
    public function set(string $key,$value,int $expire = 3600) : bool
    {
        return $this->memcached->set($key,$value,$expire);
    }
    public function delete(string $key) : bool
    {
        return $this->memcached->delete($key);
    }
    public function decrement(string $key,int $offset = 1) {
        return $this->memcached->decrement($key,$offset);
    }
    public function increment(string $key,int $offset = 1) {
        return $this->memcached->increment($key,$offset);
    }
    public function prepend(string $key,$value) : bool
    {
        return $this->memcached->prepend($key,$value);
    }
    public function replace(string $key,$value,int $expire = 3600) : bool
    {
        return $this->memcached->replace($key,$value,$expire);
    }
    public function error()
    {
        return $this->memcached->getResultMessage();
    }
    public function quit()
    {
        return $this->memcached->quit();
    }
}