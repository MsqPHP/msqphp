<?php declare(strict_types = 1);
namespace msqphp\base\filter;

use msqphp\base;
use msqphp\traits;

final class Filter
{
    use traits\CallStatic;
    use traits\Polymorphic;
    /**
     * html过滤, 输出纯html文本
     * @param  miexd $value 值
     * @return miexd
     */
    public static function html() {
        return static::polymorphic(
            func_get_args(),
            [
                'string', function (string $value) : string {
                    return htmlspecialchars($value, ENT_QUOTES);
                }
            ],
            [
                'array', function (array $value) : array {
                    return array_map('static::html', $value);
                }
            ],
            [
                'number', function ($value) {
                    return (string)$value;
                }
            ],
            [
                'miexd', function ($value) {
                    throw new FilterException('不支持的格式');
                }
            ]
        );
    }
    /**
     * 转义
     * @param  miexd $value 值
     * @return miexd
     */
    public static function slashes() {
        return static::polymorphic(
            func_get_args(),
            [
                'string', function (string $value) : string {
                    return addslashes($value);
                }
            ],
            [
                'array', function (array $value) : array {
                    return array_map('static::slashes', $value);
                }
            ],
            [
                'number', function ($value) {
                    return (string)$value;
                }
            ],
            [
                'miexd', function ($value) {
                    throw new FilterException('不支持的格式');
                }
            ]
        );
    }
}