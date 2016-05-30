<?php declare(strict_types = 1);
return [
// 默认不记录日志
    'LOG_RECORD'            =>  false,
    // 日志记录类型 默认为文件方式 首字母大写，其余小写
    'LOG_TYPE'              =>  'File',
    // 允许记录的日志级别
    'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR',
    'LOG_PATH'              =>  'Log',
    // 日志文件大小限制
    'LOG_FILE_SIZE'         =>  2097152,
    // 是否记录异常信息日志
    'LOG_EXCEPTION_RECORD'  =>  false,
];