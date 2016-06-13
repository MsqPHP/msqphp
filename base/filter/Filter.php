<?php declare(strict_types = 1);
namespace msqphp\base\filter;

use msqphp\base;
use msqphp\traits;

final class Filter
{
    // use traits\CallStatic;
    use traits\Polymorphic;
    /**
     * html过滤, 输出纯html文本
     * @param  miexd $value 值
     * @return miexd
     */
    public static function html($value) {
        // if (is_string($value)) {
        //     return htmlspecialchars($value, ENT_QUOTES);
        // } elseif(is_array($value)) {
        //     return array_map('static::html', $value);
        // } else {
        //     return $value;
        // }
        return static::polymorphic(
            func_get_args(),
            [
                'array', function (array $value) : array {
                    return array_map('static::html', $value);
                }
            ],
            [
                'string', function (string $value) : string {
                    return htmlspecialchars($value, ENT_QUOTES);
                }
            ],
            [
                'miexd', function ($value) {
                    return (string)$value;
                }
            ]
        );
    }
    /**
     * 转义
     * @param  miexd $value 值
     * @return miexd
     */
    public static function slashes($value) {
        if(is_array($value)) {
            return array_map('static::slashes', $value);
        } elseif(is_string($value)) {
            return addslashes($value);
        } else {
            return $value;
        }
    }
}