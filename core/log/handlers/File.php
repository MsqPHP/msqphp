<?php declare(strict_types = 1);
namespace msqphp\core\log\handlers;

class File implements LoggerHandlerInterface
{
    private $config = [
        'path' => '',
        'max_size'      => 2097152,
        'deep'          => 0,
        'extension'     => '.log',
    ];
    public function __construct(array $config)
    {
        $this->config = $config = array_merge($this->config, $config);
        if (!is_dir($config['path'])) {
            throw new LoggerHandlerException('日志目录不存在');
        }
    }
    private function getFilePath(string $level) : string
    {
        return $this->config['path'].date('Y-m-d').'_'.$level.$this->config['extension'];
    }
    public function log(string $level, string $message, array $context = [])
    {
        base\file\File::append($this->getFilePath('log'), $message . PHP_EOL);
    }
    public function emergency(string $message, array $context = [])
    {
        base\file\File::append($this->getFilePath('emergency'), $message);
    }
    public function alert(string $message, array $context = [])
    {
        base\file\File::append($this->getFilePath('alert'), $message);
    }
    public function critical(string $message, array $context = [])
    {
        base\file\File::append($this->getFilePath('critical'), $message);
    }
    public function error(string $message, array $context = [])
    {
        base\file\File::append($this->getFilePath('error'), $message);
    }
    public function warning(string $message, array $context = [])
    {
        base\file\File::append($this->getFilePath('warning'), $message);
    }
    public function notice(string $message, array $context = [])
    {
        base\file\File::append($this->getFilePath('notice'), $message);
    }
    public function info(string $message, array $context = [])
    {
        base\file\File::append($this->getFilePath('info'), $message);
    }
    public function debug(string $message, array $context = [])
    {
        base\file\File::append($this->getFilePath('debug'), $message);
    }
}