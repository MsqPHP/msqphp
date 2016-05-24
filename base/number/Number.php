<?php declare(strict_types = 1);
namespace msqphp\base\number;

use msqphp\base;
use msqphp\traits;

class Number
{
    use traits\CallStatic;
    /**
     * 数字转换文件大小格式
     * @param  int     $size  数字
     * @param  bool    $round 是否取整
     * @return string
     */
    public static function byte($size, $round = true) : string
    {
        if (!is_numeric($size)) {
            throw new NumberException($size.'不是一个有效数字');
        }
        //单位进制
        static $units = [' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB'];

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
    /**
     * 判断一个数是否为质数
     * @param  int     $number 数
     * @return bool
     */
    public static function isPrimeNumber(int $number) : bool
    {
        if ($number < 2) {
            return false;
        }
        for ($i = 2, $max = sqrt($number); $i <= $max; ++$i) {
            if (0 === $number % $i) {
                return false;
            }
        }
        return true;
    }
    /**
     * 得到指定范围内所有质数
     * @param  int    $from 开始
     * @param  int    $to   结束
     * @return array
     */
    public static function getPrimeNumber(int $from, int $to) : array
    {
        $result = [];
        for ($i = $from; $i < $to; ++$i) {
            static::isPrimeNumber($i) && $result[] = $i;
        }
        return $result;
    }
    /**
     * 绝对值
     * @param int|float $number  数字
     * @return miexd
     */
    public static function abs($number)
    {
        return $number > 0 ? $number : -$number;
    }
    /**
     * 平方根
     * @param int|float $number  数字
     * @return miexd
     */
    public static function sqrt($number)
    {
        return sqrt($number);
    }
}