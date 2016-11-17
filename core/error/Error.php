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
            ini_set('error_log', static::getPath('storage').'error.log');
        }
        // 载入错误类,设置错误函数处理方式
        static::register();
    }
    public static function register() : void
    {
        set_error_handler('\msqphp\core\error\Error::handler', E_ALL);
    }
    public static function unregister() : void
    {
        restore_error_handler();
    }
    public static function handler(int $errno , string $errstr, string $errfile , int $errline) : bool
    {
        if ('cli' === \msqphp\Environment::getRunMode()) {
            echo '错误代码:'.$errno."\n".'错误信息:'.$errstr."\n".'错误文件:'.$errfile."\n".'错误行号:'.$errline."\n";
        } else {
            echo '<style type="text/css">.error{border: 1px solid black;}.error td {border: 1px solid black;}</style><table class="error"><tr><td>文件</td><td>行号</td><td>错误代码</td><td>错误信息</td></tr><tr><td>'.$errfile.'</td><td>'.$errline.'</td><td>'.$errno.'</td><td>'.$errstr.'</td></tr></table>';
        }
        app()->log->level('error')->message('错误代码:'.$errno."\n".'错误信息:'.$errstr."\n".'错误文件:'.$errfile."\n".'错误行号:'.$errline."\n")->recode();
        return true;
    }
}