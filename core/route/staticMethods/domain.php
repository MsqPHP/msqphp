<?php declare(strict_types = 1);
namespace msqphp\core\route;
/**
 * 域名限制
 *
 * @function domain
 *
 * @param   miexd      $domain  域名,支持多
 * @param   Closure    $func   调用函数
 * @param   array      $args   函数参数
 *
 * @throws  RouteException
 * @return  void
 */
return function ($domain, \Closure $func, array $args = []) {
    if (static::$matched) {
        return;
    }
    //获得当前域名
    static::$info['domain'] = static::$info['domain'] ?? $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'];

    if (in_array(static::$info['domain'], (array)$domain)) {
        call_user_func_array($func, $args);
    }
};