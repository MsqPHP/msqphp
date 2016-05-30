<?php declare(strict_types = 1);
namespace msqphp\test;

//新建一个测试类
$test = new Test();

$test->testAll(__DIR__.DIRECTORY_SEPARATOR.'test');
//测试当前目录下所有文件