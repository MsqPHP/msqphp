<?php declare(strict_types = 1);
namespace msqphp\core\debug;

use msqphp\base;
use msqphp\core;

class Debug
{
    public static function init()
    {
        if (0 === APP_DEBUG) {
            static::production();
        } else {
            static::development();
        }
        set_exception_handler(['msqphp\\core\\exception\\Exception','handler']);
        set_error_handler(['msqphp\\core\\error\\Error','handler'], E_ALL);
        function_exists('getrusage') && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && define('PHP_START_CPU', getrusage());
        function_exists('memory_get_usage') && define('PHP_START_MEM' , memory_get_usage());
        switch (APP_DEBUG) {
            case 5:
                require base\response\Response::unavailable();
                break;
            case 4:
                require \msqphp\Environment::getPath('framework').'Test.php';
                exit;
            case 3:
                define('NO_CACHE', true);
            case 2:
                define('NO_VIEW', true);
            case 1:
                define('NO_STATIC', true);
                break;
            default:
                throw new DebugException('未知的访问模式');
        }
    }
    public static function development()
    {
        //设置错误级别最高
        error_reporting(E_ALL);
        //错误显示
        ini_set('display_errors', 'On');
        //取消日志记录
        ini_set('log_errors', 'Off');
    }
    public static function production()
    {
        //设置错误级别最低
        error_reporting(0);
        //错误不显示
        ini_set('display_errors', 'Off');
        //开启日志记录
        ini_set('log_errors', 'On');
    }
}
