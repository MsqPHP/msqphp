<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;

trait RouteRouleTrait
{
    //所有路由规则
    private static $roule         = [];

    /**
     * 路由规则检测
     *
     * @param  string $value 值
     * @param  string $roule 规则键
     *
     * @return bool
     */
    private static function checkRoule(string $value, string $roule) : bool
    {
        //规则存在
        //如果是string则正则
        //则调用函数
        return isset(static::$roule[$roule]) && (
            is_string(static::$roule[$roule])
            ? 0 !== preg_match(static::$roule[$roule], $value)
            : static::$roule[$roule]($value));
    }

    /**
     * 添加一条规则
     * @param  string  $key   规则键
     * @param  string  $func  正则
     * @param  Closure $func  回调函数
     *     @example
     *         Route::addRoule(string ':all', function() : bool {
     *             return true;
     *         });
     */
    public static function addRoule(string $key, $func)
    {
        if (is_string($func) || $func instanceof \Closure) {
            static::$roule[$key] = $func;
        } else {
            throw new RouteException('错误的路由规则');
        }
    }
}