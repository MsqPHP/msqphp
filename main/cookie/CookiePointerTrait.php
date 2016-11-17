<?php declare(strict_types = 1);
namespace msqphp\main\cookie;

trait CookiePointerTrait
{
    /**
     * @param  string $prefix 前缀
     * @param  string $key 键
     * @param  string|array $value 值(如果是数组则需要加密)
     * @param  int    $expire 前缀
     * @param  string $path 路径
     * @param  string $domain 域名
     * @param  bool $secure 安全传输
     * @param  bool $httponly httponly
     * @param  bool $transcoding bool
     * @param  bool   $decode 解密
     * @param  bool   $encode 加密
     */

    private $pointer = [];

    public function __construct()
    {
        static::initStatic();
        $this->init();
    }

    // 初始化
    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }

    // 设置前缀
    public function prefix(string $prefix) : self
    {
        $this->pointer['prefix'] = $prefix;
        return $this;
    }

    // 设置键
    public function key(string $key) : self
    {
        $this->pointer['key'] = $key;
        return $this;
    }
    // 设置值
    public function value($value) : self
    {
        $this->pointer['value'] = $value;
        return $this;
    }
    // 设置前缀
    public function expire(int $expire) : self
    {
        $this->pointer['expire'] = $expire;
        return $this;
    }
    // 设置路径
    public function path(string $path) : self
    {
        $this->pointer['path'] = $path;
        return $this;
    }
    // 设置域名
    public function domain(string $domain) : self
    {
        $this->pointer['domain'] = $domain;
        return $this;
    }
    // 设置 是否仅https
    public function secure(bool $secure = true) : self
    {
        $this->pointer['secure'] = $secure;
        return $this;
    }
    // 设置 httponly
    public function httponly(bool $httponly = true) : self
    {
        $this->pointer['httponly'] = $httponly;
        return $this;
    }
    // 是否url转码
    public function transcoding(bool $transcoding = false) : self
    {
        $this->pointer['transcoding'] = $transcoding;
        return $this;
    }
    // 值解密
    public function decode(bool $decode = true) : self
    {
        $this->pointer['decode'] = $decode;
        return $this;
    }
    // 值加密
    public function encode(bool $encode = true) : self
    {
        $this->pointer['encode'] = $encode;
        return $this;
    }
}