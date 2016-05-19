<?php declare(strict_types = 1);
defined('APP_DEBUG') || die('不合理访问');
return [
    // 数据库类型
    'type'               =>  'mysql',
    // 服务器地址
    'host'               =>  'localhost',
    // 端口
    'port'               =>  '3306',
    // 用户名
    'username'           =>  'root',
    // 密码
    'password'           =>  'password',
    // 数据库名
    'name'               =>  'databasename',
    // 数据库编码默认采用utf8
    'charset'            =>  'utf8',
    // 数据库表前缀
    'prefix'             =>  '',
    // 数据库连接参数
    'params'             =>  [\PDO::ATTR_PERSISTENT=>true],
    //多数据库
    'more'               =>  [
        'sql1'           =>  [
            'type'               =>  'mysql',
            'host'               =>  'localhost',
            'port'               =>  '3306',
            'username'           =>  'root',
            'password'           =>  'password',
            'name'               =>  'databasename',
            'charset'            =>  'utf8',
            'prefix'             =>  '',
        ],
    ],
];