<?php declare(strict_types = 1);
defined('APP_DEBUG') || die('不合理访问');
return [
    /*
        如果开启请在在函数 vendor\composer\ClassLoader.php\includeFile函数中添加一行代码$GLOBALS['autoloader_files'][] = $file;
        并保证每次composer更新时,去修改一次.
     */
    'autoload_cache' => false,
    /*
        缓存时间,只要你敢,你可以设置的无限大,一个月什么的就差不多,然后将入口文件引入autoload类删除
        然后,就没有然后了
    */
    'autoload_time'  => 86400,
    /*
        controller 缓存时间,同上,只要你敢
     */
    'controller_info_time' => 86400,

    //默认加密盐
    'salt'              =>  '87923e71dbee64e957fc132f276f04bf',
];