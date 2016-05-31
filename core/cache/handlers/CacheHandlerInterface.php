<?php declare(strict_types = 1);
namespace msqphp\core\cache\handlers;

Interface CacheHandlerInterface
{
    //构造函数
    public function __construct(array $config);
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
    public function available(string $key) : bool;
    //得到缓存信息
    public function get(string $key);
    //递增
    public function increment(string $key, int $offset) : int;
    //递减
    public function decrement(string $key, int $offset) : int;
    //设置缓存
    public function set(string $key, $value, int $expire);
    //清除缓存
    public function delete(string $key);
    //清空
    public function clear();
}