<?php declare(strict_types = 1);
namespace msqphp\core\log\handlers;

interface LoggerHandlerInterface
{
    public function __construct(array $config);
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @return null
     */
    public function log(string $level, string $message);
    public function emergency(string $message);
    public function alert(string $message);
    public function critical(string $message);
    public function error(string $message);
    public function warning(string $message);
    public function notice(string $message);
    public function info(string $message);
    public function debug(string $message);
    public function exception(string $message);
}