<?php declare(strict_types = 1);
namespace msqphp\main\log;

final class Log
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
    const EXCEPTION = 'exception';
    const SUCCESS   = 'success';

    private static $handler = null;
    private static $config = [
    ];

    private $pointer = [];
    public function __construct()
    {
        $this->init();
    }
    // 抛出异常
    private static function exception(string $message) : void
    {
        throw new LogException($message);
    }
    private static function initStatic() : void
    {
        // 初始化过直接返回
        static $inited = false;

        if ($inited) {
            return;
        }
        $inited = true;

        static::initCnnfigAndGetHandler();
    }
    private static function initCnnfigAndGetHandler() : void
    {
        static::$config  = $config = array_merge(static::$config,app()->config->get('log'));
        static::$handler = static::initHandler($config['default_handler'], $config['handlers_config'][$config['default_handler']]);
    }
    private static function initHandler(string $type, array $config) : handlers\LoggerHandlerInterface
    {
        // 载入默认处理器接口文件
        require __DIR__ . DIRECTORY_SEPARATOR . 'handlers' . DIRECTORY_SEPARATOR . 'LoggerHandlerInterface.php';
        // 连接处理器文件路径
        $file = __DIR__.DIRECTORY_SEPARATOR.'handlers'.DIRECTORY_SEPARATOR.$type.'.php';
        // 如果不存在查找lib目录下是否存在
        is_file($file) || $file = str_replace(\msqphp\Environment::getPath('library'), \msqphp\Environment::getPath('framework'), $file);
        // 仍不存在,异常
        is_file($file) || static::exception($type.'日志处理类不存在');
        // 加载处理类文件
        require $file;
        // 拼接类名
        $class = __NAMESPACE__.'\\handlers\\'.$type;
        // 创建类
        return new $class($config);
    }

    public function init() : self
    {
        static::initStatic();
        $this->pointer = [];
        return $this;
    }
    public function msg(string $message) : self
    {
        return $this->message($message);
    }
    public function message(string $message) : self
    {
        $this->pointer['message'] = $message;
        return $this;
    }
    public function level(string $level) : self
    {
        $this->pointer['level'] = $level;
        return $this;
    }
    public function type(string $type) : self
    {
        return $this->level($type);
    }
    public function content(array $content) : self
    {
        $this->pointer['content'] = $content;
        return $this;
    }
    public function recode() : void
    {
        $pointer = $this->pointer;
        $level = $pointer['level'] ?? '';
        $message = $pointer['message'] ?? '';
        $content = $pointer['content'] ?? [];
        if (in_array(strtolower($level), static::$config['level'])) {
            static::$handler->record($level, $message, $content);
        }
    }
}