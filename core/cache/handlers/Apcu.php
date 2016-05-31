<?php declare(strict_types = 1);
namespace msqphp\core\cache\handlers;

final class Apcu implements CacheHandlerInterface
{
    private $config = [
        'length' => 0
    ];

    public function __construct(array $config)
    {
        if (!function_exists('apcu_cache_info') || !ini_get('apcu.enabled')) {
            throw new CacheHandlerException('require Apcu support');
        }

        $this->config = array_merge($this->config, $config);
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
        return apcu_exists($key);
    }
    //得到缓存信息
    public function get(string $key)
    {
        if (false !== $value = apcu_fetch($key)) {
            return $value;
        } else {
            throw new CacheHandlerException($key.'缓存键不存在');
        }
    }
    public function increment(string $key, int $offset) : int
    {
        if (false === $num = apcu_inc($key, $offset)) {
            throw new CacheHandlerException($key.'缓存值无法自增');
        } else {
            return $num;
        }
    }
    public function decrement(string $key, int $offset) : int
    {
        if (false === $num = apcu_dec($key, $offset)) {
            throw new CacheHandlerException($key.'缓存值无法自减');
        } else {
            return $num;
        }
    }
    //设置缓存
    public function set(string $key, $value, int $expire)
    {
        if (false === apcu_store($key, $value, $expire)) {
            throw new CacheHandlerException($key.'缓存值无法设置');
        }
        $this->config['length'] > 0 && $this->queue($key);
    }
    //清楚缓存
    public function delete(string $key)
    {
        if (false === apcu_delete($key)) {
            throw new CacheHandlerException($key.'缓存值无法删除');
        }
    }
    //清除指定前缀的所有过期的缓存
    public function clear()
    {
        if (false === apcu_clear_cache('user')) {
            throw new CacheHandlerException('缓存无法清空');
        }
    }
    private function queue($key)
    {
        $queue_name = '__msq_cache_list__';
        if (!apcu_exists($queue_name)) {
            apcu_add($queue_name, [$key]);
        } else {
            $queue = apcu_fetch($queue_name);
            //如果未找到则添加
            false === array_search($key, $queue) && array_push($queue, $key);
            //如果队列长度大于配置长度
            if (count($queue) > $this->length) {
                //移除第一个
                $old_key = array_shift($queue);
                //删除对应文件
                apcu_delete($old_key);
            }
            //重新写入
            apcu_store($queue_name, $queue);
        }
    }
}