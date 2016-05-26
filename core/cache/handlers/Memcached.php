<?php declare(strict_types = 1);
namespace msqphp\core\cache\handlers;

final class Memcached implements CacheHandlerInterface
{
    //处理类
    private $handler = null;
    //available时获取的值
    private $vaule   = [];
    private $config  = [
        'length'  => 0,
    ];
    //构造函数
    public function __construct(array $config)
    {
        if (!extension_loaded('memcached')) {
            throw new CacheHandlerException('require memcached support');
        }
        //获得实例
        $this->handler = $memcached = new \Memcached($config['name']);

        //是否是原始的
        if ($memcached->isPristine()) {
            if(!empty($config['options'])) {
                //参数设置
                $memcached->setOptions($config['options']);
            }
            $memcached->addServer($config['server'], $config['port'], $config['weight']);
            if ($config['multi']) {
                $memcached->addServers($config['servers']);
            }
        }
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
        if (false === $value = $this->handler->get($key)) {
            return false;
        } else {
            $this->value[$key] = $value;
            return true;
        }
    }
    //得到缓存信息
    public function get(string $key)
    {
        if ($this->value[$key] !== null) {
            return $this->value[$key];
        } else {
            return $this->handler->get($key);
        }
    }
    public function increment(string $key, int $offset)
    {
        $this->handler->increment($key, $offset);
    }
    public function decrement(string $key, int $offset)
    {
        $this->handler->decrement($key, $offset);
    }
    //设置缓存
    public function set(string $key, $value, int $expire)
    {
        $this->handler->set($key, $value, $expire);
        //如果限制了最大储存数, 调用队列
        $this->config['length'] > 0 && $this->queue($key);
    }
    //清楚缓存
    public function delete(string $key)
    {
        $this->handler->delete($key);
    }
    //清除指定前缀的所有过期的缓存
    public function clear()
    {
        $this->handler->flush();
    }
    private function queue($key)
    {
        $handler = $this->handler;
        $queue_name = '__msq_cache_list__';
        if (false === $queue = $handler->get($queue_name)) {
            $handler->set($queue_name, [$key]);
        } else {

            //如果未找到则添加
            false === array_search($key, $queue) && array_push($queue, $key);
            //如果队列长度大于配置长度
            if (count($queue) > $this->config['length']) {
                //移除第一个
                $old_key = array_shift($queue);
                //删除对应文件
                $handler->delete($old_key);
            }
            //重新写入
            $handler->set($queue_name, $queue);
        }
    }
}