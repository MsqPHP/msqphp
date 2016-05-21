<?php declare(strict_types = 1);
return [
    /**
     * mysql[type,host,port,username.password,name,chatset,prefix,params]
     * cubrid[name,host,port,username,password,charset]
     * mssql[host,name,charset,username,password]
     * sybase[host,name,charset,username,password]
     * dblib[host,name,charset,username,password]
     */
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
    'name'               =>  'daname',
    // 数据库编码默认采用utf8
    'charset'            =>  'utf8',
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
            'name'               =>  'daname',
            'charset'            =>  'utf8',
        ],
    ],
];