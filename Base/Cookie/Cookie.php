<?php declare(strict_types = 1);
namespace Core\Base\Cookie;
/**
 * Cookie
 * @class Cookie
 * @method
 *     @return  $this           cookie设置
 *          init()                        初始化当前操作cookie(避免上次操作后留下一些内容什么的)
 *          key(string $key)              设置当前编辑cookie键
 *          value($value)                 设置当前编辑cookie值
 *          expire(int $expire)           设置当前编辑cookie过期时间
 *          path(string $path)            设置当前编辑cookie路径
 *          domain(string $domain)        设置当前编辑cookie域名
 *          secure(bool $secure=true)     设置当前编辑cookie安全传输
 *          httponly(bool $httponly=treu) 设置当前编辑cookie httponly
 *     @return  $this          cookie辅助设置
 *          prefix(string $prefix)        设置当前编辑cookie前缀
 *          transcoding(bool $transcoding = false) 设置当前编辑cookie是否url转码
 *          decode(bool $decode)          设置当前编辑cookie是否加密
 *          encode(bool $encode)          设置当前编辑cookie是否解密
 *     @return string|array
 *          get()           得到cookie值或所有cookie(不判断是否存在,不存在直接返回所有cookie)
 *     @return  bool
 *          exists()        当前操作值是否存在    
 *          set()           设置cookie
 *          clear()         删除指定前缀cookie
 *          delete()        删除ccokie
 *          empty()         清空cookie
 * @example
 *      $this->init()->key('username')->value('root')->expire(3600)->set;
 *          设置一个 键: 前缀 . username
 *                   值: root
 *                   过期时间:time()+3600
 *                   的cookie;
 * @example
 *      $this->init()->key('username')->prefix('core_')->value('root')->encode(true)->expire(200)->set();
 *          设置一个 键: core_username
 *                   值: (加密后)root
 *                   过期时间:time()+200
 *                   的cookie;
 * @example
 *      if ($this->init()->key('username')->exists()) $this->get();
 *           获取键值为 前缀 . username的cookie
 * @example
 *      $this->init()->get();
 *           获取所有cookie;
 * @example
 *      框架使用
 *          Controller 下
 *              $this->cookie = 本对象
 *              采用动态加载,随用随加载
 */
class Cookie
{
    //当前cookie实例
    private static $instance = null;
    //cookie前缀
    private $prefix          = '';
    //cookie过期时间
    private $expire          = 3600;
    //cookie路径
    private $path            ='';
    //cookie域名
    private $domain          = '';
    //cookie 安全(只在https中传输)
    private $secure          = false;
    //cookie httponly
    private $httponly        = false;
    //cookie 过滤
    private $filter          = false;
    //cookie 加密
    private $encode          = false;
    //cookie 转码
    private $transcoding     = false;
    //当前脚本所有的cookie
    private $cookies         = [];
    //当前编辑的cookie
    private $cookie          = [];

    /**
     * cookie构建函数
     * @param array $config Config,可以为空,但不可以不传数组
     */
    private function __construct(array $config = [])
    {
        $config         = $config ?: require \Core\Framework::$config_path.'cookie.php';
        $this->prefix   = $prefix = $config['prefix'] ?? '';
        $this->expire   = $config['expire'] ?? 3600;
        $this->path     = $config['path'] ?? '/';
        $this->domain   = $config['domain'] ?? '';
        $this->secure   = $config['secure'] ?? false;
        $this->httponly = $config['httponly'] ?? false;
        $this->filter   = $config['filter'] ?? false;
        $this->encode   = $config['encode'] ?? false;
        $this->transcoding = $config['transcoding'] ?? true;
        //是否过滤cookie
        if (isset($config['filter']) && $config['filter']) {
            $len = strlen($prefix);
            $_COOKIE = array_filter(
                $_COOKIE,
                function($key) use ($len,$prefix) {
                    return substr($key,0,$len) === $prefix;
                },
                ARRAY_FILTER_USE_KEY
            );
        }
        $this->cookies = & $_COOKIE;
    }

    /**
     * 获得当前对象
     * @return $this
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new Cookie();
        }
        return static::$instance;
    }
    /**
     * 初始化当前操作cookie
     * @return $this
     */
    public function init()
    {
        $this->cookie = [];
        return $this;
    }
    /**
     * 设置当前编辑cookie键
     * @param  string $key 键
     * @return $this
     */
    public function key(string $key)
    {
        $this->cookie['key'] = $key;
        $this->setValue();
        return $this;
    }
    /**
     * 设置当前编辑cookie值
     * @param  string|array $value 值(如果是数组则需要加密)
     * @return $this
     */
    public function value($value)
    {
        $this->cookie['value'] = $value;
        return $this;
    }
    /**
     * 设置当前编辑cookie前缀
     * @param  int    $expire 前缀
     * @return $this
     */
    public function expire(int $expire)
    {
        $this->cookie['expire'] = $expire;
        return $this;
    }
    /**
     * 设置当前操作cookie路劲
     * @param  string $path 路径
     * @return $this
     */
    public function path(string $path)
    {
        $this->cookie['path'] = $path;
        return $this;
    }
    /**
     * 设置当前操作cookie域名
     * @param  string $domain 域名
     * @return $this
     */
    public function domain(string $domain)
    {
        $this->cookie['domain'] = $domain;
        return $this;
    }
    /**
     * 设置当前操作cookie安全传输
     * @param  bool|boolean $secure 安全传输
     * @return $this
     */
    public function secure(bool $secure = true)
    {
        $this->cookie['secure'] = $secure;
        return $this;
    }
    /**
     * 设置当前操作cookie httponly
     * @param  bool|boolean $httponly httponly
     * @return $this
     */
    public function httponly(bool $httponly = true)
    {
        $this->cookie['httponly'] = $httponly;
        return $this;
    }
    /**
     * 设置当前操作cookie前缀
     * @param  string $prefix 前缀
     * @return $this
     */
    public function prefix(string $prefix)
    {
        $this->cookie['prefix'] = $prefix;
        $this->setValue();
        return $this;
    }
    /**
     * 是否url转码
     * @param  bool|boolean $transcoding bool
     * @return $this
     */
    public function transcoding(bool $transcoding = false)
    {
        $this->cookie['transcoding'] = $transcoding;
        return $this;
    }
    /**
     * 当前操作cookie值解密
     * @param  bool   $decode 解密
     * @return $this
     */
    public function decode(bool $decode = true)
    {
        if (!isset($this->cookie['encode'])) {
            $this->cookie['decode'] = $decode;
            $decode && $this->decodeValue();
        }
        return $this;
    }
    /**
     * 当前操作cookie值加密
     * @param  bool   $encode [description]
     * @return $this
     */
    public function encode(bool $encode = true)
    {
        if (!isset($this->cookie['encode'])) {
            $this->cookie['encode'] = $encode;
            $encode && $this->encodeValue();
        }
        return $this;
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
        return isset($this->cookie['key']) ? $this->cookie['value'] : $this->cookies;
    }
    /**
     * 设置cookie值
     * @return bool
     */
    public function set() : bool
    {
        //默认加密
        $this->encode($this->encode);

        //获得cookie信息
        $cookie   = $this->cookie;
        $key      = $this->getKey();
        $value    = (string) $cookie['value'];
        $expire   = time() + ( $cookie['expire'] ?? $this->expire );
        $path     = $cookie['path']     ?? $this->path;
        $domain   = $cookie['domain']   ?? $this->domain;
        $secure   = $cookie['secure']   ?? $this->secure;
        $httponly = $cookie['httponly'] ?? $this->httponly;

        $func     = ( $cookie['transcoding'] ?? $this->transcoding ) ? 'setcookie' : 'setrawcookie';

        if (!$func($key,$value,$expire,$path,$domain,$secure,$httponly)) {
            throw new CookieException('未知错误,无法定义cookie');
            return false;
        }

        $this->cookies[$key] = $value;
        return true;
    }
    /**
     * 删除cookie
     * @return bool
     */
    public function delete() : bool
    {
        return setcookie($this->getKey(),'',0);
    }
    /**
     * 清空cookie
     * @return bool
     */
    public function empty() : bool
    {
        foreach ($this->cookies as $key => $value) {
            setcookie($key,'',0);
        }
        $this->cookies = [];
        return true;
    }
    /**
     * 清除指定前缀cookie
     * @return bool
     */
    public function clear() : bool
    {
        //前缀
        $prefix = $this->cookie['prefix'] ?? $this->prefix;
        //遍历
        foreach (array_filter(
                $this->cookies,
                function($key) use ($len,$prefix) {
                    return substr($key,0,$len) === $prefix;
                },
                ARRAY_FILTER_USE_KEY
            ) as $key => $value) {
            setcookie($key,'',0);
            unset($this->cookies[$key]);
        }
        return true;
    }
    /**
     * 得到当前操作cookie正确键值
     * @param  string $key 
     * @return string
     */
    private function getKey() : string
    {
        if (!isset($this->cookie['key'])) {
            throw new CookieException('未选定任意cookie');
            return false;
        }
        $pre = $this->cookie['prefix'] ?? $this->prefix;
        $pre_len = strlen($pre);
        $key = $this->cookie['key'];
        while (0 === strpos($key,$pre)) {
            $key = substr($key,$pre_len);
        }
        return $pre.$key;
    }
    /**
     * 设置cookie值
     * @return void
     */
    private function setValue()
    {
        $this->cookie['value'] = $this->cookies[$this->getKey()] ?? '';
    }
    /**
     * 加密当前cookie值
     * @return void
     */
    private function encodeValue()
    {
        $this->cookie['value'] = serialize($this->cookie['value']);
    }
    /**
     * 解密当前cookie值
     * @return void
     */
    private function decodeValue()
    {
        $this->cookie['value'] = unserialize($this->cookie['value']);
    }
}