<?php declare(strict_types = 1);
namespace msqphp\core\error;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

final class Error
{
    public static function register()
    {
        set_error_handler('\msqphp\core\error\Error::handler', E_ALL);
    }
    public static function unregister()
    {
        restore_error_handler();
    }
    public static function handler(int $errno , string $errstr, string $errfile , int $errline) : bool
    {
        if ('cli' === \msqphp\Environment::getSapi()) {
            echo '错误代码:'.$errno."\n";
            echo '错误信息:'.$errstr."\n";
            echo '错误文件:'.$errfile."\n";
            echo '错误行号:'.$errline."\n";
        } else {
            echo '<style type="text/css">.error{border: 1px solid black;}.error td {border: 1px solid black;}</style><table class="error"><tr><td>文件</td><td>行号</td><td>错误代码</td><td>错误信息</td></tr><tr><td>'.$errfile.'</td><td>'.$errline.'</td><td>'.$errno.'</td><td>'.$errstr.'</td></tr></table>';
        }
        return true;
    }
}