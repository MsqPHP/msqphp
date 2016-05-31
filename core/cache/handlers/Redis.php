<?php declare(strict_types = 1);
namespace msqphp\core\cache\handlers;

final class Redis extends CacheHandlerInterface
{
    private $config = [
        'socket_type' => 'tcp',
        'host'        =>'127.0.0.1',
        'password'    =>'',
        'port'        =>6379,
        'timeout'     =>0
    ];
    private $redis = null;
    //构造函数
    public function __construct(array $config)
    {
        throw new CacheHandlerException('未完善');

        if (!extension_loaded('redis')) {
            throw new CacheHandlerException('require Redis support');
        }

        $config = array_merge($config, $this->config);

        $this->redis = $redis = new Redis;

        try {
            if ('unix' === $config['socket_type']) {
                $success = $redis->connect($config['socket']);
            } else {
                $success = $redis->connect($config['host'], $config['port'], $config['timeout']);
            }
            if (!$success) {
                throw new CacheHandlerException('redis无法连接');
            }
            if (isset($config['password']) && ! $redis->auth($config['password'])) {
                throw new CacheHandlerException('redis无法验证');
            }
        } catch (RedisException $e) {
            throw new CacheHandlerException('redis无法连接');
        }
    }
    /**
     * @param  array  $config 缓存驱动配置
     * @param  string $key    缓存键
     * @param  string $val    缓存值
     * @param  int    $expire 缓存有效期
     * @param  int    $offset 偏移量
     * @throws 对应Exception
     * @return bool   是否存在
     * @return void   如果出错抛出异常, 返回什么false
     */
    //是否可用
    public function available(string $key) : bool
    {

    }
    //得到缓存信息
    public function get(string $key)
    {
        return $this->redis->get($key);
    }
    //递增
    public function increment(string $key, int $offset) : int
    {
        return $this->redis->incr($id, $offset);
    }
    //递减
    public function decrement(string $key, int $offset) : int
    {

        return $this->redis->decr($id, $offset);
    }
    //设置缓存
    public function set(string $key, $value, int $expire)
    {
        return $this->redis->set($key, $value, $expire);
    }
    //清除缓存
    public function delete(string $key)
    {
        $this->redis->delete($key);
    }
    //清空
    public function clear()
    {
        return $this->redis->flushDB();
    }
}