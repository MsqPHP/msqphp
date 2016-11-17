<?php declare(strict_types = 1);
namespace msqphp\main\log\handlers;

use msqphp\base;

class File implements LoggerHandlerInterface
{
    private $config = [
        'path'          => '',
        'max_size'      => 2097152,
        'files'         => 1,
        'deep'          => 0,
        'extension'     => '.log',
    ];

    public function __construct(array $config)
    {
        $config = array_merge($this->config, $config);

        if (!is_dir($config['path'])) {
            throw new LoggerHandlerException('日志目录不存在');
        }
        if (!is_writable($config['path'])) {
            throw new LoggerHandlerException('日志目录不可写');
        }

        $config['path'] = realpath($config['path']) . DIRECTORY_SEPARATOR;

        $this->config = $config;
    }

    public function record(string $level, string $message, array $context = [])
    {
        base\file\File::append(
            $this->config['path'].date('Y-m-d').DIRECTORY_SEPARATOR.$level.random_int(1, $this->config['files']).$this->config['extension']
            , '['.date('Y-m-d H:i:s').']' . $level . ':' .$message .PHP_EOL .(empty($context) ? '' : '{' . PHP_EOL. implode(PHP_EOL, $context) . PHP_EOL . '}' . PHP_EOL)
            , true
        );
    }

    public function emergency(string $message, array $context = [])
    {
        static::record('emergency', $message, $context = []);
    }
    public function alert(string $message, array $context = [])
    {
        static::record('alert', $message, $context = []);
    }
    public function critical(string $message, array $context = [])
    {
        static::record('critical', $message, $context = []);
    }
    public function error(string $message, array $context = [])
    {
        static::record('error', $message, $context = []);
    }
    public function warning(string $message, array $context = [])
    {
        static::record('warning', $message, $context = []);
    }
    public function notice(string $message, array $context = [])
    {
        static::record('notice', $message, $context = []);
    }
    public function info(string $message, array $context = [])
    {
        static::record('info', $message, $context = []);
    }
    public function debug(string $message, array $context = [])
    {
        static::record('debug', $message, $context = []);
    }
    public function exception(string $message, array $context = [])
    {
        static::record('exception', $message, $context = []);
    }
    public function success(string $message, array $context = [])
    {
        static::record('success', $message, $context);
    }
}