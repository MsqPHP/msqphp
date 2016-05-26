<?php declare(strict_types = 1);
namespace msqphp\core\cookie;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

final class Cookie
{
    use traits\Instance;
    private $config   = [
        //前缀
        'prefix'      =>'',
        //过期时间
        'expire'      =>3600,
        //路径
        'path'        =>'/',
        //域名
        'domain'      =>'',
        //https
        'secure'      =>false,
        //httpoly
        'httponly'    =>false,
        //过滤
        'filter'      =>false,
        //url转义
        'transcoding' =>false,
        //加密
        'encode'      =>false,
    ];
    //当前脚本所有的cookie
    private $cookies   = [];
    //当前编辑的cookie
    private $pointer          = [];

    /**
     * cookie构建函数
     * @param array $config Config, 可以为空, 但不可以不传数组
     */
    private function __construct()
    {
        $this->config = $config = array_merge($this->config, core\config\Config::get('cookie'));
        //是否过滤cookie
        if ($config['filter']) {
            $prefix  = $config['prefix'];
            $len     = strlen($prefix);
            $_COOKIE = array_filter($_COOKIE, function($key) use ($len, $prefix) {
                    return substr($key, 0, $len) === $prefix;
            }, ARRAY_FILTER_USE_KEY);
        }
        $this->cookies = & $_COOKIE;
    }
    /**
     * 初始化当前操作cookie
     * @return self
     */
    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }
    /**
     * 设置当前操作cookie前缀
     * @param  string $prefix 前缀
     * @return self
     */
    public function prefix(string $prefix) : self
    {
        $this->pointer['prefix'] = $prefix;
        $this->setValue();
        return $this;
    }
    /**
     * 设置当前编辑cookie键
     * @param  string $key 键
     * @return self
     */
    public function key(string $key) : self
    {
        $this->pointer['key'] = $key;
        $this->setValue();
        return $this;
    }
    /**
     * 设置当前编辑cookie值
     * @param  string|array $value 值(如果是数组则需要加密)
     * @return self
     */
    public function value($value) : self
    {
        $this->pointer['value'] = $value;
        return $this;
    }
    /**
     * 设置当前编辑cookie前缀
     * @param  int    $expire 前缀
     * @return self
     */
    public function expire(int $expire) : self
    {
        $this->pointer['expire'] = $expire;
        return $this;
    }
    /**
     * 设置当前操作cookie路劲
     * @param  string $path 路径
     * @return self
     */
    public function path(string $path) : self
    {
        $this->pointer['path'] = $path;
        return $this;
    }
    /**
     * 设置当前操作cookie域名
     * @param  string $domain 域名
     * @return self
     */
    public function domain(string $domain) : self
    {
        $this->pointer['domain'] = $domain;
        return $this;
    }
    /**
     * 设置当前操作cookie 是否仅https
     * @param  bool $secure 安全传输
     * @return self
     */
    public function secure(bool $secure = true) : self
    {
        $this->pointer['secure'] = $secure;
        return $this;
    }
    /**
     * 设置当前操作cookie httponly
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
     * 当前操作cookie值解密
     * @param  bool   $decode 解密
     * @return self
     */
    public function decode(bool $decode = true) : self
    {
        if (isset($this->pointer['decode'])) {
            return $this;
        } else {
            $this->pointer['decode'] = $decode;
            $decode && $this->decodeValue();
            return $this;
        }
    }
    /**
     * 当前操作cookie值加密
     * @param  bool   $encode [description]
     * @return self
     */
    public function encode(bool $encode = true) : self
    {
        if (isset($this->pointer['encode'])) {
            return $this;
        } else {
            $this->pointer['encode'] = $encode;
            $encode && $this->encodeValue();
            return $this;
        }
    }
    /**
     * cookie是否存在
     * @return bool
     */
    public function exists() : bool
    {
        return isset($this->cookies[$this->getKey()]);
    }
    /**
     * 得到当前操作cookie值 或者 得到全部cookie值
     * @return string|array
     */
    public function get()
    {
        return isset($this->pointer['key']) ? $this->pointer['value'] : $this->cookies;
    }
    /**
     * 设置cookie值
     * @throws CookieException
     * @return void
     */
    public function set()
    {
        //默认加密
        $this->encode($this->config['encode']);

        //获得cookie信息
        $cookie   = $this->pointer;
        $key      = $this->getKey();

        $this->isSetValue();

        $value    = (string) $cookie['value'];
        $expire   = time() + ( $cookie['expire'] ?? $this->config['expire'] );
        $path     = $cookie['path']     ?? $this->config['path'];
        $domain   = $cookie['domain']   ?? $this->config['domain'];
        $secure   = $cookie['secure']   ?? $this->config['secure'];
        $httponly = $cookie['httponly'] ?? $this->config['httponly'];

        $func     = ( $cookie['transcoding'] ?? $this->config['transcoding'] ) ? 'setcookie' : 'setrawcookie';

        if (!$func($key, $value, $expire, $path, $domain, $secure, $httponly)) {
            throw new CookieException('未知错误, 无法定义cookie');
        }
        $this->cookies[$key] = $value;
    }
    /**
     * 删除cookie
     * @throws CookieException
     * @return void
     */
    public function delete()
    {
        setcookie($this->getKey(), '', 0);
    }
    /**
     * 清除指定前缀cookie
     * @throws CookieException
     * @return void
     */
    public function clear()
    {
        //遍历
        foreach ($this->cookies as $key => $value) {
            setcookie($key, '', 0);
        }
        $this->cookies = [];
    }
    /**
     * 得到当前操作cookie正确键值
     * @param  string $key
     * @return string
     */
    private function getKey() : string
    {
        if (isset($this->pointer['key'])) {
            return ($this->pointer['prefix'] ?? $this->config['prefix']).$this->pointer['key'];
        } else {
            throw new CookieException('未选定任意cookie');
        }
    }
    /**
     * 设置cookie值
     * @return void
     */
    private function setValue()
    {
        $key = $this->getKey();
        if (isset($this->cookies[$key])) {
            $this->pointer['value'] = $this->cookies[$key];
        }
    }
    /**
     * 是否设置了cookie值
     *
     * @throws CookieException
     * @return  void
     */
    private function isSetValue()
    {
        if (!isset($this->pointer['value'])) {
            throw new CookieException('未定义cookie值,无法设置');
        }
    }
    /**
     * 加密当前cookie值
     * @return void
     */
    private function encodeValue()
    {
        $this->isSetValue();

        $this->pointer['value'] = base\crypt\Crypt::encrypt(serialize($this->pointer['value']));
    }
    /**
     * 解密当前cookie值
     * @return void
     */
    private function decodeValue()
    {
        $this->isSetValue();

        $this->pointer['value'] = base\crypt\Crypt::decrypt(unserialize($this->pointer['value']));
    }
}