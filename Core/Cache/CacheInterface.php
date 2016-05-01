<?php declare(strict_types = 1);
namespace Msqphp\Core\Cache;

Interface CacheInterface
{
	//构造函数
	abstract public function __construct(array $config);
	/**
	 * @param  array  $config 缓存驱动配置
	 * @param  string $key    缓存键
	 * @param  string $val    缓存值
	 * @param  int    $expire 缓存有效期
	 * @return bool 是否成功 | 是否存在
	 */
	//是否可用
	/*if (defined('NO_CACHE')) {
            return false;
        }
    */
	abstract public function available(string $key) : bool;
	//设置缓存
	/*if (defined('NO_CACHE')) {
            return true;
        }
    */
	abstract public function set(string $key, $value, int $expire = 1800) : bool;
	//得到缓存信息
	abstract public function get(string $key);
	//清楚缓存
	abstract public function delete(string $key) : bool ;
	//清除指定前缀的所有过期的缓存
	abstract public function clear() : bool ;
}