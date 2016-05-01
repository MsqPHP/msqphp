<?php declare(strict_types = 1);
namespace Msqphp\Core\Cache\Driver;

use Msqphp\Core;
use Msqphp\Base;

class File implements Core\Cache\CacheInterface {
    
    private $path       = '';
    private $extension  = '';
    private $prefixion  = '';

    private $handler     = null;

    public function __construct(array $config) {
        $this->path      = realpath($config['path']).DIRECTORY_SEPARATOR;        
        $this->extension = $config['extension'];
        $this->prefixion = $config['prefixion'];
        $this->handler    = Base\File\File::getInstance();
    }
    
    /**
     * cache是否存在
     * @param  string $key cache键
     * @return boolen      是否存在
     */
    public function available(string $key) : bool {
        if (defined('NO_CACHE')) {
            return false;
        }
        $now  = time();
        //获得文件路径
        $file = $this->getCacheFilePath($key);
        if (false === is_file($file)) {
            return false;
        }
        //获得过期时间
        $deadtime = $this->handler->read($file,10);
        //是否为空
        return (int)$deadtime > $now;
    }
    /**
     * 设置缓存
     * @param string      $key    键
     * @param string      $value  值
     * @param int         $expire 存在时间
     */
    public function set(string $key, $value, int $expire = 1800) : bool
    {
        if (defined('NO_CACHE')) {
            return true;
        }
        //获得文件路径
        $file     = $this->getCacheFilePath($key);
        //值
        $value    = (string)(time() + $expire) . $this->serialize($value);
        //存储
        return $this->handler->save($file,$value,true);
    }
    /**
     * 得到cache
     * @param  string $key 键
     * @return string      值
     */
    public function get(string $key)
    {
        //获得文件路径
        $file = $this->getCacheFilePath($key);
        //得到内容
        $value = $this->handler->get($file);
        //去除前十个字符（过期时间）
        $value = $this->unserialize(substr($value, 10));
        return $value;
    }
    /**
     * 删除指定缓存
     * @param  string $key 键
     * @return boolen      是否成功
     */
    public function delete(string $key) : bool {
        //获得文件路径
        $file = $this->getCacheFilePath($key);
        //检测文件是否存在
        return $this->handler->delete($file,true);
    }
    /**
     * 清除所有过期的缓存
     * @return boolen      是否成功
     */
    public function clear() : bool
    {
        $handler = $this->handler;
        //所有缓存文件
        $files = $handler->getAllFileByType($this->path,$this->extension);
        //当前时间
        $now = time();
        //遍历
        foreach ($files as $file) {
            $deadtime = (int) $handler->read($file,10);
            if ($deadtime < $now) {
                $bool = true && $handler->deleteFile($file);
            }
        }
        return $bool;
    }
    /**
     * 得到缓存文件目录
     * @param  string $key 键
     * @return string
     */
    private function getCacheFilePath(string $key) : string
    {
        return $this->path.$this->prefixion.$key.$this->extension;
    }
    private function serialize($value) : string
    {
        return serialize($value);
    }
    private function unserialize(string $value)
    {
        return unserialize($value);
    }
}