<?php declare(strict_types = 1);
namespace msqphp\core\error;

use msqphp\core;
use msqphp\base;

class Error
{
    public static function handler(int $errno , string $errstr, string $errfile , int $errline)
    {
        echo '<style type="text/css">.error{border: 1px solid black;}.error td {border: 1px solid black;}</style><table class="error"><tr><td>文件</td><td>行号</td><td>错误代码</td><td>错误信息</td></tr><tr><td>'.$errfile.'</td><td>'.$errline.'</td><td>'.$errno.'</td><td>'.$errstr.'</td></tr></table>';
    }
}