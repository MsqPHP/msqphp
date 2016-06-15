<?php declare(strict_types = 1);
namespace msqphp\base\number;
/**
 * 判断一个数是否为质数
 * @function isPrimeNumber
 * @param  int     $number 数
 * @return bool
 */
return function (int $number) : bool {
    if ($number < 2) {
        return false;
    }
    for ($i = 2, $max = sqrt($number); $i <= $max; ++$i) {
        if (0 === $number % $i) {
            return false;
        }
    }
    return true;
};