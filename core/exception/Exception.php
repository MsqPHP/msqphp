<?php declare(strict_types = 1);
namespace msqphp\core\exception;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

class Exception
{
    public static function handler($e)
    {
        echo $e->getMessage();
        echo '在'.$e->getFile().'文件中第'.$e->getLine().'行';
        echo '<style type="text/css">.error{border: 1px solid black;}.error td {border: 1px solid black;}</style>';
        echo '<table class="error"><tr><td>文件</td><td>行号</td><td>函数</td><td>参数</td></tr>';
        foreach ($e->getTrace() as $value) {
            echo '<tr>';
            echo '<td>'.($value['file'] ?? '').'</td>';
            echo '<td>'.($value['line'] ?? '').'</td>';
            echo '<td>'.(isset($value['class']) ? $value['class'] . $value['type'] . $value['function'] : $value['function']).'</td>';
            echo '<td><pre>'.(isset($value['args']) ? var_export($value['args'],true) : 'null').'</pre></td>';
            echo '</tr>';
        }
        echo '</table>';

        echo '<hr/>';

    }
}