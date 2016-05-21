<?php declare(strict_types = 1);
return [
    //处理器
    'handler'        =>  'File',
    //前缀
    'prefix'     =>  'sess_',
    //session名
    'name'          =>  'CORE_SESSION',
    //配置
    'handlers_config'        =>  [
        'File'              =>  [
            'path'              =>  __DIR__.'/../storage/session/',//Application/Session
            'extension'         =>  '.session',
        ],
    ],
];