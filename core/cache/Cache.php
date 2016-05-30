<?php declare(strict_types = 1);
namespace msqphp\core\cache;

use msqphp\core;
use msqphp\traits;

final class Cache
{
    use traits\Instance;
    //所有处理类
    private $handlers   = [];
    //缓存配置
    private $config     = [
        'multi'                =>  false,
        'sports'               =>  ['File'],
        'default_handler'      =>  'File',
        'prefix'               =>  '',
        'expire'               =>  3600,
        //处理类配置
        'handlers_config'      =>  [],
    ];
    //当前处理类的指针
    private $pointer             = [];
    /**
     * 构造方法 $config
     * @param array $config [description]
     */
    private function __construct()
    {
        //缓存配置
        $config           = array_merge($this->config, core\config\Config::get('cache'));
        //处理类支持列表
        $config['sports'] = $config['multi'] ? $config['sports'] : [ $config['default_handler'] ];
        $this->config     = $config;
        //缓存处理类接口文件
        require __DIR__.DIRECTORY_SEPARATOR.'handlers'.DIRECTORY_SEPARATOR.'CacheHandlerInterface.php';
        //设置处理类
        $this->setHandler($config['default_handler']);
    }
    /**
     * 初始化本对象, 并告知所用那一种处理类
     * @param  string $handler [description]
     * @return self
     */
    public function init(string $handler = '', array $config=[]) : self
    {
        //将当前操作cache初始化
        $this->pointer = [];
        //设置处理类
        $this->setHandler($handler, $config);
        return $this;
    }
    public function handler(handlers\CacheHandlerInterface $handler) : self
    {
        $this->pointer['handler'] = $handler;
        return $this;
    }
    /**
     * 设置当前缓存处理键前缀
     * @param  string $prefix 前缀
     * @return self
     */
    public function prefix(string $prefix) : self
    {
        $this->pointer['prefix'] = $prefix;
        return $this;
    }
    /**
     * 设置当前处理缓存键
     * @param  string $key 键
     * @return self
     */
    public function key(string $key) : self
    {
        $this->pointer['key'] = $key;
        return $this;
    }
    /**
     * 当前处理缓存值
     * @param  miexd $value 值
     * @return self
     */
    public function value($value) : self
    {
        $this->pointer['value'] = $value;
        return $this;
    }
    /**
     * 当前处理缓存偏移量
     * @param  int $offset 偏移量
     * @return self
     */
    public function offset(int $offset) : self
    {
        $this->pointer['offset'] = $offset;
        return $this;
    }
    /**
     * 设置当前处理缓存过期时间
     * @param  int    $expire 过期时间
     * @return bool
     */
    public function expire(int $expire) : self
    {
        $this->pointer['expire'] = $expire;
        return $this;
    }


    /**
     * 当前处理缓存键是否存在
     * @return bool
     */
    public function exists() : bool
    {
        if (defined('NO_CACHE')) {
            return false;
        } else {
            return $this->pointer['handler']->available($this->getKey());
        }
    }
    public function available() : bool
    {
        return $this->exists();
    }
    /**
     * 得到当前处理缓存键对应值
     * @return miexd
     */
    public function get()
    {
        try {
            return $this->pointer['handler']->get($this->getKey());
        } catch(handlers\CacheHandlerException $e) {
            throw new CacheException($e->getMessage());
        }
    }
    /**
     * 自增
     * @throws CacheException
     * @return void
     */
    public function inc()
    {
        $this->increment();
    }
    public function increment()
    {
        try {
            return $this->pointer['handler']->increment($this->getKey(), $this->pointer['offset'] ?? 1);
        } catch(handlers\CacheHandlerException $e) {
            throw new CacheException($e->getMessage());
        }
    }
    /**
     * 自减
     * @throws CacheException
     * @return void
     */
    public function dec()
    {
        $this->decrement();
    }
    public function decrement()
    {
        try {
            $this->pointer['handler']->decrement($this->getKey(), $this->pointer['offset'] ?? 1);
        } catch(handlers\CacheHandlerException $e) {
            throw new CacheException($e->getMessage());
        }
    }
    /**
     * 设置当前处理缓存键 对应值
     * @throws CacheException
     * @return void
     */
    public function set()
    {
        if (!defined('NO_CACHE')) {
            try {
                $this->pointer['handler']->set($this->getKey(), $this->pointer['value'], $this->pointer['expire'] ?? $this->config['expire']);
            } catch(handlers\CacheHandlerException $e) {
                throw new CacheException($e->getMessage());
            }
        }
    }
    /**
     * 删除当前处理缓存键
     * @throws CacheException
     * @return void
     */
    public function delete()
    {
        try {
            $this->pointer['handler']->delete($this->getKey());
        } catch(handlers\CacheHandlerException $e) {
            throw new CacheException($e->getMessage());
        }
    }
    /**
     * 清楚所有过期缓存
     * @throws CacheException
     * @return void
     */
    public function clear()
    {
        try {
            $this->pointer['handler']->clear();
        } catch(handlers\CacheHandlerException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * 得到缓存真实键
     * @return string
     */
    private function getKey() : string
    {
        if (isset($this->pointer['key'])) {
            return ($this->pointer['prefix'] ?? $this->config['prefix']).$this->pointer['key'];
        } else {
            throw new CacheException('未选择任意缓存键');
        }
    }
    /**
     * 设置对应的处理类
     * @param  string $type    处理类类型
     * @param  array  $config  处理类配置
     * @throws CacheException
     * @return void
     */
    private function setHandler(string $type = '', array $config = [])
    {
        //为空取默认
        $type = $type ?: $this->config['default_handler'];
        //如果不在支持列表中
        if (!in_array($type, $this->config['sports'])) {
            throw new CacheException($type . ' 缓存处理器不支持');
        }

        if (empty($config)) {
            $config      = $this->config['handlers_config'][$type];
            $key         = md5($type);
        } else {
            $config      = array_merge($this->config['handlers_config'][$type], $config);
            $key         = md5(serialize($config).$type);
        }

        $this->handlers[$key] = $this->pointer['handler'] = $this->handlers[$key] ?? $this->initHandler($type, $config);
    }
    /**
     * 加载并返回处理类
     * @param  string $type    处理类类型
     * @param  array  $config  处理类配置
     * @throws CacheException
     * @return 处理类
     */
    private function initHandler(string $type, array $config) : handlers\CacheHandlerInterface
    {
        static $files  = [];
        if (!isset($files[$type])) {
            //载入cache处理类文件
            $file = __DIR__.DIRECTORY_SEPARATOR.'handlers'.DIRECTORY_SEPARATOR.$type.'.php';
            //如果不存在查找lib目录下是否存在
            if (!is_file($file)) {
                $file = str_replace(\msqphp\Environment::getPath('library'), \msqphp\Environment::getPath('framework'), $file);
                if (!is_file($file)) {
                    throw new CacheException($type.'缓存处理类不存在');
                }
            }
            require $file;
            $files[$type] = true;
        }
        //拼接类名
        $class = __NAMESPACE__.'\\handlers\\'.$type;
        //创建类
        return new $class($config);
    }
}