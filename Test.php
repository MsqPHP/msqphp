<?php declare(strict_types = 1);
namespace msqphp;

final class Test
{
    public static function run()
    {
        set_time_limit(300);

        //版本检测
        if (!version_compare(PHP_VERSION, '7.0.0', '>')) {
            throw new Exception('require php 7.0, 版本低点可能也没什么');
        }

        if (!function_exists('mb_get_info')) {
            throw new Exception('没有开启mb扩展, 可能也没什么');
        }
        if (!class_exists('PDO')) {
            throw new Exception('没有开启Pdo展, 可能也没什么');
        }

        require \msqphp\Environment::getPath('test') . 'test.php';
    }
}