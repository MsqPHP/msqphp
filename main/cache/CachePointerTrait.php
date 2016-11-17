<?php declare(strict_types = 1);
namespace msqphp\main\cache;

trait CachePointerTrait
{
    // 当前处理类的指针
    private $pointer             = [];

    /**
     * @param  string  $type   处理类种类
     * @param  array   $config 处理类配置
     * @param  handlers\CacheHandlerInterface $handler  处理类
     * @param  string  $prefix 前缀
     * @param  string  $key    键
     * @param  miexd   $value  值
     * @param  int     $offset 偏移量
     * @param  int     $expire 过期时间
     *
     */

    public function __construct()
    {
        $this->init();
    }
    // 初始化指针
    public function init() : self
    {
        static::initStatic();
        // 将当前操作cache初始化
        $this->pointer = [];
        return $this;
    }
    // 缓存处理器类型
    public function type(string $type) : self
    {
        $this->pointer['type'] = $type;
        return $this;
    }
    // 缓存处理器配置
    public function config(array $config) : self
    {
        $this->pointer['config'] = $config;
        return $this;
    }
    // 设置处理类
    public function handler(handlers\CacheHandlerInterface $handler) : self
    {
        $this->pointer['handler'] = $handler;
        return $this;
    }
    // 设置当前缓存处理键前缀
    public function prefix(string $prefix) : self
    {
        $this->pointer['prefix'] = $prefix;
        return $this;
    }
    // 设置当前处理缓存键
    public function key(string $key) : self
    {
        $this->pointer['key'] = $key;
        return $this;
    }
    // 当前处理缓存值
    public function value($value) : self
    {
        $this->pointer['value'] = $value;
        return $this;
    }
    // 当前处理缓存偏移量
    public function offset(int $offset) : self
    {
        $this->pointer['offset'] = $offset;
        return $this;
    }
    // 设置当前处理缓存过期时间
    public function expire(int $expire) : self
    {
        $this->pointer['expire'] = $expire;
        return $this;
    }
}