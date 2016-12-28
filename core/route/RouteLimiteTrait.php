<?php declare(strict_types = 1);
namespace msqphp\core\route;

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

    public static function limit($may, $truevalue, \Closure $func, array $args) : void
    {
        static::$matched || (in_array($truevalue, (array)$may) && call_user_func_array($func, $args));
    }

    // SSL协议, 即https限制
    public static function ssl(\Closure $func, array $args = []) : void
    {
        static::$matched || (static::getProtocol() === 'https' && call_user_func_array($func, $args));
    }
    public static function https(\Closure $func, array $args = []) : void
    {
        static::$matched || (static::getProtocol() === 'https' && call_user_func_array($func, $args));
    }

    // 来自url限制
    public static function referer($referer, \Closure $func, array $args = []) : void
    {
        static::limit($referer, static::getReferer(), $func, $args);
    }

    // ip限制
    public static function ip($ip, \Closure $func, array $args = []) : void
    {
        static::limit($ip, static::getIp(), $func, $args);
    }

    // 端口限制
    public static function port ($port, \Closure $func, array $args = []) : void
    {
        static::limit($port, static::getPort(), $func, $args);
    }

    // 域名限制
    public static function domain($domain, \Closure $func, array $args = []) : void
    {
        static::limit($domain, static::getDomain(), $func, $args);
    }
}