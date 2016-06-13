<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;

/**
 * ip限制
 *
 * @function ip
 *
 * @param   miexd      $ip     ip,支持多
 * @param   Closure    $func   调用函数
 * @param   array      $args   函数参数
 *
 * @throws  RouteException
 * @return  void
 */
return function ($port, \Closure $func, array $args = []) {
    if (static::$matched) {
        return;
    }
    static::$info['ip'] = static::$info['ip'] ?? base\ip\Ip::get();
    if (in_array(static::$info['ip'], (array)$ip)) {
        call_user_func_array($func, $args);
    }
};