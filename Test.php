<?php

//版本检测
version_compare(PHP_VERSION, '7.0.0','>') || exit('require php 7.0,版本低点可能也没什么');
function_exists('mb_get_info')            || exit('没有开启mb扩展,可能也没什么');
class_exists('PDO')            || exit('没有开启Pdo展,可能也没什么');

require __DIR__.DIRECTORY_SEPARATOR.'Test'.DIRECTORY_SEPARATOR.'Test.php';

$test = new \Core\Test\Test();

$test->testAll(__DIR__);
// require __DIR__.DIRECTORY_SEPARATOR.'FrameworkTest.php';

// $test = new \Core\FrameworkTest();
// $test->testStart();
// $app_path = \Core\Framework::$app_path;

// $test->testAll(__DIR__,'\Core','');

// $test->testAll($app_path,'\Test','');
