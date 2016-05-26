<?php declare(strict_types = 1);
namespace msqphp\base\ini;

use msqphp\base;
use msqphp\traits;

final class Ini
{
    use traits\CallStatic;

    public static function encode(array $data) : string
    {
        $string = '';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $string .= '[' . $key . ']' . PHP_EOL;
                foreach ($value as $k => $v) {
                    if (is_array($v) || is_object($v)) {
                        throw new IniException('数组维数过多,无法转化为ini格式');
                    }
                    $string .= $k . '=' . $v . PHP_EOL;
                }
            } else {
                throw new IniException('数组值不是数组,无法转化为ini格式');
            }
        }
        return trim($string, PHP_EOL);
    }

    public static function decode(string $ini)
    {
        return parse_ini_string($ini);
    }
}