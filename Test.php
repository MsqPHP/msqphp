<?php declare(strict_types = 1);
namespace msqphp;

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

set_time_limit(300);

//测试类
require __DIR__.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'Test.php';
//新建一个测试类
$test = new \msqphp\test\Test();

$test->testAll(__DIR__.DIRECTORY_SEPARATOR.'test');
//测试当前目录下所有文件