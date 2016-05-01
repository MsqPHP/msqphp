<?php declare(strict_types = 1);
namespace Core\Base\Arr;

class Arr
{
    /**
     * 冒泡排序
     * @param  array  $arr array
     * @return array
     */
    public static function bubbleSort(array $arr) : array
    {
        $len = count($arr);
        for ($i=1;$i<$len;++$i) {
            for ($k=0;$k<$len-$i;++$k) {
                $arr[$k] > $arr[$k+1] && list($arr[$k],$arr[$k+1]) = array($arr[$k+1],$arr[$k]);
            }
        }
        return $arr;
    }
    /**
     * 选择排序
     * @param  array  $arr array
     * @return array
     */
    public static function selectSort(array $arr) : array
    {
        $len=count($arr);
        for ($i=0;$i<$len-1;++$i) {
            $p = $i;
            for ($j=$i+1;$j<$len;++$j) {
                $arr[$p] > $arr[$j] && $p = $j;
            }
            $p !== $i && list($arr[$p],$arr[$i]) = array($arr[$i],$arr[$p]);
        }
        return $arr;
    }
    /**
     * 插入排序
     * @param  array  $arr array
     * @return array
     */
    public static function insertSort(array $arr) : array
    {
        $len = count($arr);
        for ($i=1;$i<$len;++$i) {
            $tmp = $arr[$i];
            for ($j=$i-1;$j>=0;--$j) {
                $tmp < $arr[$j] && list($arr[$j+1],$arr[$j]) = array($arr[$j],$tmp);
            }
        }
        return $arr;
    }
    /**
     * 快速排序
     * @param  array $arr array
     * @return array
     */
    public static function quickSort($arr) : array
    {
        //数组长度
        $l = count($arr);
        if ($l <= 1) {
            return $arr;
        }
        $mid   = $arr[0];
        $left  = [];
        $right = [];

        for (--$l;$l>0;--$l) {
            $mid > $arr[$l] && ($left[]=$arr[$l]) || ($right[]=$arr[$l]);
        }
        $left  = static::quickSort($left);
        $right = static::quickSort($right);
        return array_merge($left,[$mid],$right);
    }
}