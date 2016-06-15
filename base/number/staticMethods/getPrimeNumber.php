<?php declare(strict_types = 1);
namespace msqphp\base\number;

/**
 * 得到指定范围内所有质数
 * @function getPrimeNumber
 * @param  int    $from 开始
 * @param  int    $to   结束
 * @return array
 */
return function (int $from, int $to) : array {
    $result = [];
    for ($i = $from; $i < $to; ++$i) {
        static::isPrimeNumber($i) && $result[] = $i;
    }
    return $result;
};