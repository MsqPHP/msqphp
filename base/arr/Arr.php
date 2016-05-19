<?php declare(strict_types = 1);
namespace msqphp\base\arr;

use msqphp\base;

class Arr
{
    use base\Base;
    /**
     * 冒泡排序
     * @param  array  $arr 待排序数组
     * @return array
     */
    public static function bubbleSort(array $arr) : array
    {
        for ($i = 1, $len = count($arr); $i < $len; ++$i) {
            for ($k = 0; $k < $len - $i; ++$k) {
                $arr[$k] > $arr[$k+1] && list($arr[$k], $arr[$k+1]) = [$arr[$k+1], $arr[$k]];
            }
        }
        return $arr;
    }
    /**
     * 选择排序
     * @param  array  $arr 待排序数组
     * @return array
     */
    public static function selectSort(array $arr) : array
    {
        for ($i = 0, $len = count($arr); $i < $len - 1; ++$i) {
            $p = $i;
            for ($j = $i + 1; $j < $len; ++$j) {
                $arr[$p] > $arr[$j] && $p = $j;
            }
            $p !== $i && list($arr[$p], $arr[$i]) = [$arr[$i], $arr[$p]];
        }
        return $arr;
    }
    /**
     * 插入排序
     * @param  array  $arr 待排序数组
     * @return array
     */
    public static function insertSort(array $arr) : array
    {
        for ($i = 1, $len = count($arr); $i < $len; ++$i) {
            $tmp = $arr[$i];
            for ($j=$i-1;$j>=0;--$j) {
                $tmp < $arr[$j] && list($arr[$j+1], $arr[$j]) = [$arr[$j], $tmp];
            }
        }
        return $arr;
    }
    /**
     * 快速排序
     * @param  array  $arr 待排序数组
     * @return array
     */
    public static function quickSort(array $arr) : array
    {
        //数组长度
        $l = count($arr);
        if ($l <= 1) {
            return $arr;
        }
        $mid   = $arr[0];
        $left  = [];
        $right = [];
        for (--$l; $l > 0;--$l) {
            $mid > $arr[$l] && ($left[]=$arr[$l]) || ($right[]=$arr[$l]);
        }
        $left  = static::quickSort($left);
        $right = static::quickSort($right);
        return array_merge($left, [$mid], $right);
    }
    /**
     * 随机获得数组中单个值
     * @param  array $array    随机数组
     * @return miexd
     */
    public static function random(array $array, int $count = 1)
    {
        if ($count === 1) {
            return $array[array_rand($array , 1)];
        } else {
            $count = min($count, count($array));
            $index = array_rand($array, $count);

            return array_filter(
                $array,
                function ($key) use ($index) {
                    return in_array($key, $index);
                },
                ARRAY_FILTER_USE_KEY
            );
        }
    }
}