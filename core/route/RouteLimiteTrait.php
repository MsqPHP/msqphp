<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;

trait RouteLimiteTrait
{
    /**
     * 如果匹配则调用函数
     *
     * @param  miexd   $???? 限制名称,支持数组(多匹配)
     * @param  Closure $func 调用函数
     * @param  Array   $args 函数参数
     *
     * @throws  RouteException
     *
     * @return  void
     */

    //SSL协议, 即https限制
    public static function ssl(\Closure $func, array $args = [])
    {
        static::$info['ssl'] && call_user_func_array($func, $args);
    }
    //SSL协议, 即https限制
    public static function https(\Closure $func, array $args = [])
    {
        static::$info['ssl'] && call_user_func_array($func, $args);
    }

    //来自url限制
    public static function referer($referer, \Closure $func, array $args = [])
    {
        static::$info['referer'] = static::$info['referer'] ?? $_SERVER['HTTP_REFERER'];
        in_array(static::$info['referer'], (array)$referer) && call_user_func_array($func, $args);
    }

    //ip限制
    public static function ip($ip, \Closure $func, array $args = [])
    {
        static::$info['ip'] = static::$info['ip'] ?? base\ip\Ip::get();
        in_array(static::$info['ip'], (array)$ip) && call_user_func_array($func, $args);
    }

    //端口限制
    public static function port ($port, \Closure $func, array $args = [])
    {
        static::$info['port'] = static::$info['port'] ?? $_SERVER['SERVER_PORT'];
        in_array(static::$info['port'], (array)$port) && call_user_func_array($func, $args);
    }

    //域名限制
    public static function domain($domain, \Closure $func, array $args = [])
    {
        in_array(static::$info['domain'], (array)$domain) && call_user_func_array($func, $args);
    }
}