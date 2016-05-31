<?php declare(strict_types = 1);
namespace msqphp\base\arr;

use msqphp\base;
use msqphp\traits;

final class Arr
{
    //万能静态call
    use traits\CallStatic;


###
#  设置\获取
###
    /**
     * 设置数组值,以点为分隔符,支持到十位数组, 若键为空,则用值替换数组;
     *
     * @example
     *         $arr = ['liming'=>['username'=>'test','password'=>'123456']];
     *         Arr::set($arr, 'liming.username', 'liming');
     *         $arr ----> ['liming'=>['username'=>'liming','password'=>'123456']];
     *
     * @param  array  $array     待设置数组
     * @param  string $arr_key   数组键
     * @param  miexd  $arr_value 对应值
     *
     * @throws ArrException
     */
    public static function set(array & $array, string $arr_key, $arr_value)
    {
        //如果键为空
        if ('' === $arr_key) {
            $array = $arr_value;
            return;
        }

        //以点分割
        $key = explode('.', $arr_key);

        //结果
        $result = & $array;

        //递归
        for ($i = 0, $l = count($key); $i < $l; ++$i) {
            $result = & $result[$key[$i]];
        }

        //赋值
        $result = $arr_value;
    }
    /**
     * 获取数组值,,以点为分隔符,支持到十位数组, 若键为空,则获取整个数组值;
     *
     * @example
     *         $arr = ['liming'=>['username'=>'test','password'=>'123456']];
     *         Arr::get($arr, 'liming.username')  ----> 'test'
     *
     * @param  array  $array     待设置数组
     * @param  string $arr_key   数组键
     *
     * @throws ArrException
     * @return miexd
     */
    public static function get(array & $array, string $arr_key)
    {
        //键为空
        if ('' === $arr_key) {
            return $array;
        }
        //以点分割
        $key = explode('.', $arr_key);

        //结果
        $result = & $array;
        //递归赋值
        for ($i = 0, $l = count($key); $i < $l; ++$i) {
            $result = & $result[$key[$i]];
        }

        //返回
        return $result;
    }


###
#  排序
###
    /**
     * 按键冒泡排序
     *
     * @param  array  $arr 待排序数组
     *
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
     * 按键选择排序
     *
     * @param  array  $arr 待排序数组
     *
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
     * 按键插入排序
     *
     * @param  array  $arr 待排序数组
     *
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
     * 按键快速排序
     *
     * @param  array  $arr 待排序数组
     *
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
        return array_merge(static::quickSort($left), [$mid], static::quickSort($right));
    }
    /**
     * 获得随机获得数组中固定个值
     *
     * @param  array $array    随机数组
     * @param  array $count    随机个数(小于1取1)
     *
     * @return miexd
     */
    public static function random(array $array, int $count = 1)
    {
        //小于1,取1
        $count < 1 && $count = 1;

        if ($count === 1) {
            return $array[array_rand($array , 1)];
        } else {
            //取最小
            $count = min($count, count($array));

            //随机获得键
            $index = array_rand($array, $count);

            return array_filter($array, function ($key) use ($index) {
                return in_array($key, $index);
            }, ARRAY_FILTER_USE_KEY);
        }
    }
}