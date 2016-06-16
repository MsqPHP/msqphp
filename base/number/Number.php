<?php declare(strict_types = 1);
namespace msqphp\base\number;

use msqphp\base;
use msqphp\traits;

final class Number
{
    use traits\CallStatic;
    use traits\Polymorphic;
    /**
     * 数字转换文件大小格式
     * @param  int     $size  数字
     * @param  bool    $round 是否取整
     * @return string
     */
    public static function byte($size, bool $round = true) : string
    {
        //单位进制
        static $units = [' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB'];

        if (!is_numeric($size)) {
            throw new NumberException($size.'不是一个有效数字');
        }

        $pos = 0;

        while ($size >= 1024) {
            $size /= 1024;
            ++$pos;
        }

        //是否取整
        $round && $size = round($size);

        //返回结果
        return $size . $units[$pos];
    }
}