<?php declare(strict_types = 1);
namespace msqphp\base\filter;

use msqphp\base;
use msqphp\traits;

final class Filter
{
    use traits\CallStatic;
    /**
     * html过滤, 输出纯html文本
     * @param  miexd $value 值
     * @return miexd
     */
    public static function html($value) {
        if (is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES);
        } elseif (is_array($value)) {
            return array_map('static::html', $value);
        } elseif (is_int($value) || is_float($value)) {
            return (string)$value;
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } else {
            throw new FilterException('不支持的格式');
        }
    }
    /**
     * 转义
     * @param  miexd $value 值
     * @return miexd
     */
    public static function slashes() {
        if (is_string($value)) {
            return addslashes($value, ENT_QUOTES);
        } elseif (is_array($value)) {
            return array_map('static::slashes', $value);
        } elseif (is_int($value) || is_float($value)) {
            return (string)$value;
        } else {
            throw new FilterException('不支持的格式');
        }
    }
}