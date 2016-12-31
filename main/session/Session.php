<?php declare(strict_types = 1);
namespace msqphp\main\Session;

final class Session
{
    use SessionStaticTrait;
    use SessionPointerTrait;
    use SessionOperateTrait;


    // 抛出异常
    private static function exception(string $message) : void
    {
        throw new SessionException($message);
    }
}

trait SessionStaticTrait
{
    private static $config = [
        // 处理器
        'handler'         =>  'File',
        // 前缀
        'prefix'          =>  'sess_',
        // session名
        'name'            =>  'MSQ_SESSION',
        // 配置
        'handlers_config' =>  [],
    ];
    // 保存所有session
    private static $sessions  = [];

    private static $started = false;

    // 处世化静态类
    private static function initStatic() : void
    {
        // 初始化过直接返回
        static $inited = false;

        if ($inited) {
            return;
        }
        $inited = true;

        static::$config = $config = array_merge(static::$config, app()->config->get('session'));
        // 获取默认的驱动
        $handler = $config['handler'];
        // 当前目录下的Handlers下处理类名称.php
        $file = __DIR__.DIRECTORY_SEPARATOR.'handlers'.DIRECTORY_SEPARATOR.$handler.'.php';
        // 不存在, 将框架路径替换为对应的用户图书馆框架扩展路径
        is_file($file) || $file = str_replace(\msqphp\Environment::$framework_path, \msqphp\Environment::$library_path, $file);
        // 还不存在, 抛出异常
        is_file($file) || static::exception($handler.' 未知的session处理器');
        // ini设置
        ini_set('session.cache_expire', (string)$config['expire']);
        // 加载文件
        require $file;
        // 拼接函数类名, 例:\msqphp\core\session\session\handlers\File
        $class_name = __NAMESPACE__.'\\handlers\\'.$handler;
        // 注册并传参配置config
        session_set_save_handler(new $class_name($config['handlers_config'][$handler]), true);

        // session名设置
        session_name($config['name']);
        // session开始
        session_start();

        static::$sessions = & $_SESSION;

        static::$started = true;
    }

}

trait SessionPointerTrait
{
    // 当前操作session(所有操作型函数以此为基础)
    private $pointer   = [];

    //构造函数
    public function __construct()
    {
        $this->init();
    }

    // 初始化
    public function init() : self
    {
        $this->pointer = [];
        static::initStatic();
        return $this;
    }

    // 键
    public function key(string $key)
    {
        $this->pointer['key'] = $key;
        return $this;
    }
    // 前缀
    public function prefix(string $prefix)
    {
        $this->pointer['prefix'] = $prefix;
        return $this;
    }
    // 值
    public function value($value)
    {
        $this->pointer['value'] = $value;
        return $this;
    }
}

trait SessionOperateTrait
{
    // 存在
    public function exists()
    {
        return isset(static::$sessions[$this->getKey()]);
    }
    // 获取
    public function get()
    {
        return static::$sessions[$this->getKey()] ?? null;
    }
    public function getAll() : array
    {
        return static::$sessions;
    }
    // 设置
    public function set()
    {
        isset($this->pointer['value']) || static::exception('未设置对应session值');
        static::$sessions[$this->getKey()] = $this->pointer['value'];
    }
    // 删除
    public function delete()
    {
        unset(static::$sessions[$this->getKey()]);
    }
    // 关闭
    public function close()
    {
        static::$started && session_write_close();
        static::$started = false;
    }
    // 获得真是键
    private function getKey() : string
    {
        isset($this->pointer['key']) || static::exception('未设置对应session键');
        return ($this->pointer['prefix'] ?? static::$config['prefix']).$this->pointer['key'];
    }
}