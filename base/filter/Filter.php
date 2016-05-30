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
        if (is_array($value)) {
            return array_map('\msqphp\base\filter\Filter::html', $value);
        } elseif(is_string($value)) {
            return htmlspecialchars($value, ENT_QUOTES);
        } else {
            return $value;
        }
    }
    /**
     * 转义
     * @param  miexd $value 值
     * @return miexd
     */
    public static function slashes($value) {
        if(is_array($value)) {
            return array_map('\msqphp\base\filter\Filter::slashes', $value);
        } elseif(is_string($value)) {
            return addslashes($value);
        } else {
            return $value;
        }
    }
}