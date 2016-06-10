<?php declare(strict_types = 1);
namespace msqphp\core\exception;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

class Exception extends \Exception
{
    public function __construct(string $message = '', int $code = 0, \Exception $previous = null)
    {
        echo '<pre>';
        show(debug_print_backtrace());
        echo '</pre>';
        show($this->getLine(),$this->getFile());
        parent::__construct($message, $code, $previous);
    }
    public function toString()
    {
    }
}