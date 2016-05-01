<?php declare(strict_types = 1);
namespace Core\Base\Session;

class Session{
    static private $instance = null;
    //前缀
    private $prefixion = '';
    private $session   = [];
    private $sessions  = [];


    private function __construct(array $config = [])
    {
        $config = $config ?: require \Core\Framework::$config_path.'session.php';
        //获得默认的驱动
        $driver_type = $config['driver'];
        $this->prefixion = $config['prefixion'] ?? '';
        require __DIR__.DIRECTORY_SEPARATOR.'Driver'.DIRECTORY_SEPARATOR.$driver_type.'.php';
        //建立实例
        $class_name = __NAMESPACE__.'\\Driver\\'.$driver_type;
        //注册
        session_set_save_handler(new $class_name($config['config'][$driver_type]), true);

        session_name($config['name']);
        session_start();
    }
    static public function getInstance() {
        if (null === static::$instance) {
            static::$instance = new Session();
        }
        return static::$instance;
    }
    public function init()
    {
        $this->session = [];
        return $this;
    }
    public function exists()
    {
        return isset($_SESSION[$this->getKey()]);
    }
    public function key(string $key)
    {
        $this->session['key'] = $key;
        return $this;
    }
    public function value($value)
    {
        $this->session['value'] = $value;
        return $this;
    }
    public function get()
    {
        if (isset($this->session['key'])) {
            return $_SESSION[$this->getKey()];
        }
        return $_SESSION;
    }
    public function set() : bool
    {
        $_SESSION[$this->getKey()] = $this->session['value'];
        return true;
    }
    public function delete() : bool
    {
        unset($_SESSION[$this->getKey()]);
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
        return ($this->session['prefixion'] ?? $this->prefixion).$this->session['key'];
    }
}