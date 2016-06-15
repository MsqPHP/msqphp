<?php declare(strict_types = 1);
namespace msqphp\core\exception;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

class Exception extends \Exception
{
    public function __construct(string $message = '', int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        echo '<pre>';
        debug_print_backtrace();
        var_dump($this->getLine(),$this->getFile());
        echo '</pre>';
        exit;
    }
    public function toString()
    {
    }
}