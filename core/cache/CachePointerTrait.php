<?php declare(strict_types = 1);
namespace msqphp\core\cache;

trait CachePointerTrait
{
    //当前处理类的指针
    private $pointer             = [];

    /**
     * 初始化指针,并告知处理类种类及配置
     *
     * @param  string $type   处理类种类
     * @param  array  $config 处理类配置
     *
     * @return self
     */
    public function init(string $type = '', array $config=[]) : self
    {
        //将当前操作cache初始化
        $this->pointer = [];
        //设置处理类
        $this->pointer['handler'] = $this->setHandler($type, $config);
        return $this;
    }
    /**
     * 设置处理类
     *
     * @param  handlers\CacheHandlerInterface $handler 处理类对象
     *
     * @return self
     */
    public function handler(handlers\CacheHandlerInterface $handler) : self
    {
        $this->pointer['handler'] = $handler;
        return $this;
    }
    /**
     * 设置当前缓存处理键前缀
     *
     * @param  string $prefix 前缀
     *
     * @return self
     */
    public function prefix(string $prefix) : self
    {
        $this->pointer['prefix'] = $prefix;
        return $this;
    }
    /**
     * 设置当前处理缓存键
     *
     * @param  string $key 键
     *
     * @return self
     */
    public function key(string $key) : self
    {
        $this->pointer['key'] = $key;
        return $this;
    }
    /**
     * 当前处理缓存值
     *
     * @param  miexd $value 值
     *
     * @return self
     */
    public function value($value) : self
    {
        $this->pointer['value'] = $value;
        return $this;
    }
    /**
     * 当前处理缓存偏移量
     *
     * @param  int $offset 偏移量
     *
     * @return self
     */
    public function offset(int $offset) : self
    {
        $this->pointer['offset'] = $offset;
        return $this;
    }
    /**
     * 设置当前处理缓存过期时间
     *
     * @param  int    $expire 过期时间
     *
     * @return self
     */
    public function expire(int $expire) : self
    {
        $this->pointer['expire'] = $expire;
        return $this;
    }
}