<?php declare(strict_types = 1);
namespace msqphp\core\Session;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

final class Session
{

    use traits\Instance;

    private $config = [
        //处理器
        'handler'         =>  'File',
        //前缀
        'prefix'          =>  'sess_',
        //session名
        'name'            =>  'MSQ_SESSION',
        //配置
        'handlers_config' =>  [],
    ];
    //保存所有session
    private $sessions  = [];


    //当前操作session(所有操作型函数以此为基础)
    private $pointer   = [];


    private function __construct()
    {
        $this->config = $config = array_merge($this->config, core\config\Config::get('session'));
        //获得默认的驱动
        $handler = $config['handler'];
        //当前目录下的Handlers下处理类名称.php
        $file = __DIR__.DIRECTORY_SEPARATOR.'handlers'.DIRECTORY_SEPARATOR.$handler.'.php';
        //不存在, 将框架路径替换为对应的用户图书馆框架扩展路径
        if (!is_file($file)) {
            $file = str_replace(\msqphp\Environment::$framework_path, \msqphp\Environment::$library_path, $file);
            //还不存在, 抛出异常
            if (!is_file($file)) {
                throw new SessionException($handler.' 未知的session处理器');
            }
        }
        //加载文件
        require $file;
        //拼接函数类名, 例:\msqphp\core\session\session\handlers\File
        $class_name = __NAMESPACE__.'\\handlers\\'.$handler;
        //注册并传参配置config
        session_set_save_handler(new $class_name($config['handlers_config'][$handler]), true);
        ini_set('session.cache_expire', $config['expire']);
        //session名设置
        session_name($config['name']);
        //session开始
        session_start();

        $this->sessions = & $_SESSION;
    }
    /**
     * 初始化当前操作session
     * @return self
     */
    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }
    public function key(string $key)
    {
        $this->pointer['key'] = $key;
        return $this;
    }
    public function prefix(string $prefix)
    {
        $this->pointer['prefix'] = $prefix;
        return $this;
    }
    public function value($value)
    {
        $this->pointer['value'] = $value;
        return $this;
    }
    public function exists()
    {
        return isset($this->sessions[$this->getKey()]);
    }
    public function get()
    {
        if (isset($this->pointer['key'])) {
            return $this->sessions[$this->getKey()];
        }
        return $this->sessions;
    }
    public function set()
    {
        $this->sessions[$this->getKey()] = $this->pointer['value'];
    }
    public function delete()
    {
        unset($this->sessions[$this->getKey()]);
    }
    private function getKey() : string
    {
        return ($this->pointer['prefix'] ?? $this->config['prefix']).$this->pointer['key'];
    }
    public function __destruct()
    {
        session_destroy();
    }
}