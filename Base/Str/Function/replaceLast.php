<?php
    /**
     * 替换最后一个出现的指定字符
     * @func_name     replaceLast
     * @param  string $search  查找字符
     * @param  string $replace 替换字符
     * @param  string $subject 字符串
     * @return string
     */
return function (string $search, string $replace, string $subject) : string {
    $position = strrpos($subject, $search);
    if ($position !== false) {
        return substr_replace($subject, $replace, $position, strlen($search));
    }
    return $subject;
};