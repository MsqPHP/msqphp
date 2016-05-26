<?php declare(strict_types = 1);
namespace msqphp\base\arr;

use msqphp\base;
use msqphp\traits;

final class Arr
{
    use traits\CallStatic;
    public static function set(array & $array, string $arr_key, $arr_value)
    {
        if ('' === $arr_key) {
            $array = $value;
            return;
        }
        $key = explode('.', $arr_key);
        switch (count($key)) {
            case 1:
                $array[$key[0]] = $value;
                break;
            case 2:
                $array[$key[0]][$key[1]] = $value;
                break;
            case 3:
                $array[$key[0]][$key[1]][$key[2]] = $value;
                break;
            case 4:
                $array[$key[0]][$key[1]][$key[2]][$key[3]] = $value;
                break;
            case 5:
                $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]] = $value;
                break;
            case 6:
                $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]] = $value;
                break;
            case 7:
                $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]] = $value;
                break;
            case 8:
                $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]][$key[7]] = $value;
                break;
            case 9:
                $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]][$key[7]][$key[8]] = $value;
                break;
            case 10:
                $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]][$key[7]][$key[8]][$key[9]] = $value;
                break;
            default:
                throw new ArrException(var_export($arr_key, true).'数组键过无法设置');
        }
    }
    public static function get(array & $array, string $arr_key)
    {
        if ('' === $arr_key) {
            return $array;
        }
        $key = explode('.', $arr_key);
        switch (count($key)) {
            case 1:
                if (!isset($array[$key[0]])) {
                    throw new ArrException($arr_key.'不存在');
                } else {
                    return $array[$key[0]];
                }
            case 2:
                if (!isset($array[$key[0]][$key[1]])) {
                    throw new ArrException($arr_key.'不存在');
                } else {
                    return $array[$key[0]][$key[1]];
                }
            case 3:
                if (!isset($array[$key[0]][$key[1]][$key[2]])) {
                    throw new ArrException($arr_key.'不存在');
                } else {
                    return $array[$key[0]][$key[1]][$key[2]];
                }
            case 4:
                if (!isset($array[$key[0]][$key[1]][$key[2]][$key[3]])) {
                    throw new ArrException($arr_key.'不存在');
                } else {
                    return $array[$key[0]][$key[1]][$key[2]][$key[3]];
                }
            case 5:
                if (!isset($array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]])) {
                    throw new ArrException($arr_key.'不存在');
                } else {
                    return $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]];
                }
            case 6:
                if (!isset($array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]])) {
                    throw new ArrException($arr_key.'不存在');
                } else {
                    return $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]];
                }
            case 7:
                if (!isset($array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]])) {
                    throw new ArrException($arr_key.'不存在');
                } else {
                    return $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]];
                }
            case 8:
                if (!isset($array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]][$key[7]])) {
                    throw new ArrException($arr_key.'不存在');
                } else {
                    return $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]][$key[7]];
                }
            case 9:
                if (!isset($array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]][$key[7]][$key[8]])) {
                    throw new ArrException($arr_key.'不存在');
                } else {
                    return $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]][$key[7]][$key[8]];
                }
            case 10:
                if (!isset($array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]][$key[7]][$key[8]][$key[9]])) {
                    throw new ArrException($arr_key.'不存在');
                } else {
                    return $array[$key[0]][$key[1]][$key[2]][$key[3]][$key[4]][$key[5]][$key[6]][$key[7]][$key[8]][$key[9]];
                }
            default:
                throw new ArrException(var_export($arr_key, true).'数组键过无法获取');
        }
    }
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

            return array_filter($array, function ($key) use ($index) {
                    return in_array($key, $index);
            }, ARRAY_FILTER_USE_KEY);
        }
    }
}