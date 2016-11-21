<?php declare(strict_types = 1);
namespace msqphp\core\error;

use msqphp\core\traits;

final class Error
{
    public static function init() : void
    {
        // 错误处理方式
        if (APP_DEBUG) {
            // 设置错误级别最高
            error_reporting(E_ALL);
            // 错误显示
            ini_set('display_errors', 'On');
            // 取消日志记录
            ini_set('log_errors', 'Off');
        } else {
            // 设置错误级别最低
            error_reporting(0);
            // 错误不显示
            ini_set('display_errors', 'Off');
            // 开启日志记录
            ini_set('log_errors', 'On');
            // 日志文件
            ini_set('error_log', \msqphp\Environment::getPath('storage').'error.log');
        }
        // 载入错误类,设置错误函数处理方式
        static::register();
    }
    public static function register() : void
    {
        set_error_handler('\msqphp\core\error\Error::handler', E_ALL);
        set_exception_handler('\msqphp\core\error\Error::exceptionHandler');
    }
    public static function unregister() : void
    {
        restore_error_handler();
    }
    public static function exceptionHandler($e) : void
    {
        echo '<style type="text/css">*{margin: 0;padding: 0;}.exception{width: 80%;display: block;margin:0 auto;}.exception h3 {border: none;background: #F3F3F3;border-radius: 10px 10px 10px 10px;font-size: 1em;line-height: 3em;text-align: center;margin: 1em 0;}.exception .table{background: #F3F3F3;border: none;border-radius: 10px 10px 10px 10px;font-size: 1em;width: 100%;display: block;padding: 1em 0;margin:0 auto;}.exception table{margin:0 auto;}.exception .table h4{text-align: center;}.exception th{text-align: center;}.exception td{background: #FFFFCC;}.exception tr .num,.exception tr .line{width: 2em;text-align: center;}</style>';
        echo '<div class="exception">';
        echo '<h3>' . $e->getMessage() . '</h3>';
        echo '<div class="table">
                <h4>PHP DEBUG</h4>
                <table align="center" border="1" cellspacing="0">
                    <tr>
                        <th class="num">No.</th>
                        <th class="file">File</th>
                        <th class="line">Line</th>
                        <th class="code">Code</th>
                    </tr>
        ';

        $trace = $e->getTrace();

        array_unshift($trace, ['num'=>0, 'file'=>$e->getFile(),'line'=>$e->getLine(),'function'=>'throw']);

        for($i = 0, $l=count($trace);$i<$l;++$i) {
            $num = $i;
            $file = $trace[$i]['file'] ?? '';
            $line = $trace[$i]['line'] ?? '';
            $code = isset($trace[$i]['type']) ? $trace[$i]['class'] . $trace[$i]['type'] . $trace[$i]['function'] . '()' : $trace[$i]['function'] . '()';
        
            echo '<tr>';
            echo '<td class="num">'   . $num . '</td>';
            echo '<td class="file">' . $file . '</td>';
            echo '<td class="line">' . $line . '</td>';
            echo '<td class="code">' . $code . '</td>';
            echo '</tr>';
        }

        echo '</table></div></div>';
    }
    public static function handler(int $errno , string $errstr, string $errfile , int $errline) : bool
    {
        if (APP_DEBUG) {
            if ('cli' === \msqphp\Environment::getRunMode()) {
                echo '错误代码:'.$errno."\n".'错误信息:'.$errstr."\n".'错误文件:'.$errfile."\n".'错误行号:'.$errline."\n";
            } else {
                echo '<style type="text/css">.error{border: 1px solid black;}.error td {border: 1px solid black;}</style><table class="error"><tr><td>文件</td><td>行号</td><td>错误代码</td><td>错误信息</td></tr><tr><td>'.$errfile.'</td><td>'.$errline.'</td><td>'.$errno.'</td><td>'.$errstr.'</td></tr></table>';
            }
        } else {
            app()->log->level('error')->message('错误代码:'.$errno."\n".'错误信息:'.$errstr."\n".'错误文件:'.$errfile."\n".'错误行号:'.$errline."\n")->recode();
        }
        return true;
    }
}