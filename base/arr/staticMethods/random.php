<?php declare(strict_types = 1);
namespace msqphp\base\arr;

/**
 * 获得随机获得数组中固定个值
 * @function random
 * @param  array $array    随机数组
 * @param  array $count    随机个数(小于1取1)
 *
 * @return miexd
 */
return function (array $array, int $count = 1)
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
};