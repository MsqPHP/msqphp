<?php declare(strict_types = 1);
namespace msqphp\core\log;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

final class Log
{
    use traits\Instance;

    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';

    private $handler = null;
    private $config = [
    ];
    private $pointer = [];
    private function __construct()
    {
        $this->config  = $config = array_merge($this->config,core\config\Config::get('log'));
        $this->handler = $this->initHandler($config['default_handler'], $config['handlers_config'][$default_handler]);
    }
    public function init() : self
    {
        $this->pointer = [];
        return $this;
    }
    public function msg(string $message) : self
    {
        return $this->message($message);
    }
    public function message(string $message) : self
    {
        $this->pointet['message'] = $message;
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
    public function record()
    {
        $pointer = $this->pointer;
        $level = $pointer['level'] ?? '';
        $message = $pointer['message'] ?? '';
        if (in_array(strtolower($level, $this->config, 'level')) {
            $this->handler->log($level, $message, $content);
        }
    }
    private function initHandler(string $type, array $config)
    {
        static $files  = [];
        if (!isset($files[$type])) {
            //载入cache处理类文件
            $file = __DIR__.DIRECTORY_SEPARATOR.'handlers'.DIRECTORY_SEPARATOR.$type.'.php';
            //如果不存在查找lib目录下是否存在
            if (!is_file($file)) {
                $file = str_replace(\msqphp\Environment::getPath('library'), \msqphp\Environment::getPath('framework'), $file);
                if (!is_file($file)) {
                    throw new LogException($type.'日志处理类不存在');
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