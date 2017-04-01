<?php declare(strict_types = 1);
namespace msqphp\main\log;

final class Log
{
    use LogStaticTrait, LogParamsAndOperateTrait;
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

    // 抛出异常
    private static function exception(string $message) : void
    {
        throw new LogException($message);
    }
}

trait LogStaticTrait
{
    // 处理器
    private static $handler = null;
    // 配置
    private static $config = [];

    // 静态类初始化
    private static function initStatic() : void
    {
        // 初始化过直接返回
        static $inited = false;

        if (!$inited) {
            $inited = true;
            // 初始化配置
            static::$config  = $config = array_merge(static::$config,app()->config->get('log'));
            // 获得处理器
            static::$handler = static::initHandler($config['default_handler'], $config['handlers_config'][$config['default_handler']]);
        }
    }

    // 初始化处理器
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
}

trait LogParamsAndOperateTrait
{

    private $params = [];

    public function __construct()
    {
        $this->init();
    }
    // 添加一个pointer值
    private function setParamValue(string $key, $value) : self
    {
        $this->params[$key] = $value;
        return $this;
    }
    public function init() : self
    {
        static::initStatic();
        $this->params = [];
        return $this;
    }
    public function msg(string $message) : self
    {
        return $this->message($message);
    }
    public function message(string $message) : self
    {
        return $this->setParamValue('message', $message);
    }
    public function level(string $level) : self
    {
        return $this->setParamValue('level', $level);
    }
    public function type(string $type) : self
    {
        return $this->level($type);
    }
    public function context($context) : self
    {
        return $this->setParamValue('context', $context);
    }
    public function recode() : void
    {
        $pointer = $this->params;
        $level = $pointer['level'] ?? '';
        if (in_array(strtolower($level), static::$config['level'])) {
            static::$handler->record($level, $pointer['message'] ?? '', $pointer['context'] ?? null);
        }
    }
}