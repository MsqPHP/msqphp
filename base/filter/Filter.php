<?php declare(strict_types = 1);
namespace msqphp\base\filter;

use msqphp\base;
use msqphp\traits;

class Filter
{
    use traits\CallStatic;
    /**
     * html过滤, 输出纯html文本
     * @param  string|array $value 值
     * @return string|array        过滤后的值
     */
    public static function html($value) {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = static::html($v);
            }
            return $value;
        } elseif(is_string($value)) {
            return function_exists('htmlspecialchars') ?
                htmlspecialchars($value, ENT_QUOTES) :
                str_replace(['&', '\'', '"', '<', '>'], ['&amp;', '&quot;', '&#039;', '&lt;', '&gt;'], $value);
        } else {
                return $value;
        }
    }
    /**
     * 转义
     * @param  string|array $value 值
     * @return string|array        过滤后的值
     */
    public static function slashes($value) {
        if(is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = static::slashes($v);
            }
            return $value;
        } elseif(is_string($value)) {
                return addslashes($value);
        } else {
                return $value;
        }
    }
    /**
     * sql特殊字符过滤
     * @param  string $value 值
     * @return string        过滤后的值
     */
    public static function sql(string $str) : string {
        throw new Exception('未实现', 1);
        // $sql = ['select', 'insert', 'update', 'delete', 'union', 'into', 'load_file', 'outfile', '\'', '\/\*', '\.\.\/', '\.\/'];
        // return str_replace($sql, '', $str);
    }
}