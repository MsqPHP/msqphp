<?php declare(strict_types = 1);

//是否为调试模式
const APP_DEBUG = true;

/**
 * 调试模式开启时有效
 * HAS_CACHE  开启时可以使用缓存，否则相反。
 * HAS_VIEW   开启时启用视图缓存，否则相反
 * HAS_STATIC 开启时启用静态（真静态页面及真静态路由），否则相反
 */
const HAS_CACHE  = fasle;

const HAS_VIEW   = false;

const HAS_STATIC = false;

/**
 * 载入框架环境
 * 包括但不限于：
 *     +.载入自定义函数，常量等
 *     +.载入自动加载类并初始化
 *     +.定义两个时间常量，分别为框架初始化时间，和框架开始时间。
 *     +.设置框架目录路径
 *     +.初始化框架使用环境
 */

require __DIR__ . '/framework/base_cli.php';
require __DIR__ . '/framework/loader.php';
require __DIR__ . '/framework/function.php';
require __DIR__ . '/framework/init.php';
require __DIR__ . '/framework/user.php';

//加载命令行类
require dirname(dirname(__DIR__)).'/vendor/msqphp/framework/Cli.php';

//运行
\msqphp\Cli::run();