<?php declare(strict_types = 1);
namespace msqphp\main\queue;

final class Queue
{
    private $config = [];
    private $handler = null;

    public function __construct()
    {
        $this->config = app()->config->get('queue');
        $this->handler = new $config['handler']($config['config']['handler']);
    }
    public function in(string $value)
    {
        $this->handler->in($value);
    }
    public function out() : ?string
    {
        return $this->handler->out($value);
    }
    public function length() : int
    {
        return $this->handler->length();
    }
}