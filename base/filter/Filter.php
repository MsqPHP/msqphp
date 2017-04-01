<?php declare(strict_types = 1);
namespace msqphp\base\filter;

use msqphp\core\traits;

final class Filter
{
    use traits\CallStatic;
    // 扔出异常
    private static function exception(string $message) : void
    {
        throw new FilterException($message);
    }

    /**
     * html过滤, 输出纯html文本
     *
     * @param  miexd $value 值
     *
     * @return miexd
     */
    public static function html($value)
    {
        if (is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES);
        } elseif (is_array($value)) {
            return array_map('static::html', $value);
        } elseif (is_int($value) || is_float($value)) {
            return (string)$value;
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            return 'null';
        } else {
            static::exception('不支持的格式');
        }
    }
    /**
     * 转义
     *
     * @param  miexd $value 值
     *
     * @return miexd
     */
    public static function slashes($value)
    {
        if (is_string($value)) {
            return addslashes($value, ENT_QUOTES);
        } elseif (is_array($value)) {
            return array_map('static::slashes', $value);
        } elseif (is_int($value) || is_float($value)) {
            return (string)$value;
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            return 'null';
        } else {
            static::exception('不支持的格式');
        }
    }
}