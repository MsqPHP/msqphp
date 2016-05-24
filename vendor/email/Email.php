<?php declare(strict_types = 1);
namespace msqphp\vendor\email;

class Email
{
    private static $instance = null;

    private $pointer = [];

    public static function getInstance() : self
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }
        return static::$instance;
    }
    public function from(string $from) : self
    {
        $this->pointer['from'] = $from;
        return $this;
    }
    public function to(string $to) : self
    {
        $this->pointer['to'] = $to;
        return $this;
    }
    public function title(string $title) : self
    {
        $this->pointer['title'] = $title;
        return $this;
    }
    public function content(string $content) : self
    {
        $this->pointer['content'] = $content;
        return $this;
    }
    public function send()
    {
        $pointer = $this->pointer;
        $from = 'From:'.$pointer['from'];
        mail($pointer['to'], $pointer['title'], $pointer['content'], $from);
    }
}