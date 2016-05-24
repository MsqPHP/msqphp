<?php declare(strict_types = 1);
namespace msqphp\core\Session;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

class Session{
    use traits\Instance;

    private static $config = [
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
    private static $sessions  = [];


    //当前操作session(所有操作型函数以此为基础)
    private $session   = [];


    private function __construct()
    {
        static::$config = $config = array_merge(static::$config, core\config\Config::get('session'));
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
        //session名设置
        session_name($config['name']);
        //session开始
        session_start();

        static::$sessions = & $_SESSION;
    }
    /**
     * 初始化当前操作session
     * @return self
     */
    public function init() : self
    {
        $this->session = [];
        return $this;
    }
    public function key(string $key)
    {
        $this->session['key'] = $key;
        return $this;
    }
    public function prefix(string $prefix)
    {
        $this->session['prefix'] = $prefix;
        return $this;
    }
    public function value($value)
    {
        $this->session['value'] = $value;
        return $this;
    }
    public function exists()
    {
        return isset(static::$sessions[$this->getKey()]);
    }
    public function get()
    {
        if (isset($this->session['key'])) {
            return static::$sessions[$this->getKey()];
        }
        return static::$sessions;
    }
    public function set() : bool
    {
        static::$sessions[$this->getKey()] = $this->session['value'];
        return true;
    }
    public function delete() : bool
    {
        unset(static::$sessions[$this->getKey()]);
        return true;
    }
    public function close()
    {
        static::$instance = null;
        return session_write_close();
    }
    public function destroy() : bool
    {
        static::$instance = null;
        return session_destroy();
    }
    private function getKey() : string
    {
        return ($this->session['prefix'] ?? static::$config['prefix']).$this->session['key'];
    }
}