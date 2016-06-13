<?php declare(strict_types = 1);
namespace msqphp\core\route;
/**
 * 端口限制
 *
 * @function port
 *
 * @param   miexd      $port  端口,支持多
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
    static::$info['port'] = static::$info['port'] ?? $_SERVER['SERVER_PORT'];

    if (in_array(static::$info['port'], (array)$port)) {
        call_user_func_array($func, $args);
    }
};