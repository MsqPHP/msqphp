<?php declare(strict_types = 1);
namespace msqphp\core\cookie;

trait CookiePointerTrait
{
    /**
     * 初始化
     * @return self
     */
    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }
    /**
     * 设置前缀
     * @param  string $prefix 前缀
     * @return self
     */
    public function prefix(string $prefix) : self
    {
        $this->pointer['prefix'] = $prefix;
        return $this;
    }
    /**
     * 设置键
     * @param  string $key 键
     * @return self
     */
    public function key(string $key) : self
    {
        $this->pointer['key'] = $key;
        return $this;
    }
    /**
     * 设置值
     * @param  string|array $value 值(如果是数组则需要加密)
     * @return self
     */
    public function value($value) : self
    {
        $this->pointer['value'] = $value;
        return $this;
    }
    /**
     * 设置前缀
     * @param  int    $expire 前缀
     * @return self
     */
    public function expire(int $expire) : self
    {
        $this->pointer['expire'] = $expire;
        return $this;
    }
    /**
     * 设置路径
     * @param  string $path 路径
     * @return self
     */
    public function path(string $path) : self
    {
        $this->pointer['path'] = $path;
        return $this;
    }
    /**
     * 设置域名
     * @param  string $domain 域名
     * @return self
     */
    public function domain(string $domain) : self
    {
        $this->pointer['domain'] = $domain;
        return $this;
    }
    /**
     * 设置 是否仅https
     * @param  bool $secure 安全传输
     * @return self
     */
    public function secure(bool $secure = true) : self
    {
        $this->pointer['secure'] = $secure;
        return $this;
    }
    /**
     * 设置 httponly
     * @param  bool $httponly httponly
     * @return self
     */
    public function httponly(bool $httponly = true) : self
    {
        $this->pointer['httponly'] = $httponly;
        return $this;
    }
    /**
     * 是否url转码
     * @param  bool $transcoding bool
     * @return self
     */
    public function transcoding(bool $transcoding = false) : self
    {
        $this->pointer['transcoding'] = $transcoding;
        return $this;
    }
    /**
     * 值解密
     * @param  bool   $decode 解密
     * @return self
     */
    public function decode(bool $decode = true) : self
    {
        $this->pointer['decode'] = $decode;
        return $this;
    }
    /**
     * 值加密
     * @param  bool   $encode [description]
     * @return self
     */
    public function encode(bool $encode = true) : self
    {
        $this->pointer['encode'] = $encode;
        return $this;
    }
}