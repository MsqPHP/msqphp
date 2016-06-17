<?php declare(strict_types = 1);
namespace msqphp\core\log\handlers;

interface LoggerHandlerInterface
{
    /**
     * 配置参数
     *
     * @param array $config [description]
     */
    public function __construct(array $config);
    /**
     *
     * @param mixed $level
     * @param string $message
     * @return void
     */
    public function record(string $level, string $message);
    public function emergency(string $message);
    public function alert(string $message);
    public function critical(string $message);
    public function error(string $message);
    public function warning(string $message);
    public function notice(string $message);
    public function info(string $message);
    public function debug(string $message);
    public function exception(string $message);
    public function success(string $message);
}