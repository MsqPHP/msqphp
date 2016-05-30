<?php declare(strict_types = 1);
/**
 * APP_DEBUG
 * 0:生产模式
 * 1:有所有缓存模式的模拟模式(无静态)
 * 2:没有视图缓存的调试模式
 * 3:没有缓存的调试模式
 * 4:test模式
 * 5:维护模式(直接访问503页面)
 */
define('PHP_INIT_TIME', microtime(true));

defined('APP_DEBUG') || define('APP_DEBUG',1);


//根目录
$root = dirname(__DIR__).DIRECTORY_SEPARATOR;

//全局加载数组,实现智能autoload(上次加载过那些就加载那些,直到一个稳定之后直接加载,随机删除并判断)
$autoloader_class = [];
//自动加载类
require $root.'vendor/autoload.php';

//初始化时间(框架运行前总耗时);
define('PHP_START_TIME',microtime(true));
//加载环境类
require $root.'vendor/msqphp/framework/Environment.php';

//运行
\msqphp\Environment::run([
    'root'        => $root,
    'application' => $root . 'application',
    'public'      => $root . 'public',
    'bootstrap'   => __DIR__,
    'config'      => $root . 'config',
    'storage'     => $root . 'storage',
    'resources'   => $root . 'resources',
    'library'     => $root . 'library/msqphp/framework',
    'framework'   => $root . 'vendor/msqphp/framework',
]);