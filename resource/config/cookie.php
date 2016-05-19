<?php declare(strict_types = 1);
defined('APP_DEBUG') || die('不合理访问');
return [
    // Cookie前缀 避免冲突
    'prefix'                =>  'msq_',
    // Cookie默认有效期
    'expire'                =>  3600,
    // Cookie默认路径
    'path'                  =>  '/',
    // Cookie默认有效域名
    'domain'                =>  '',
    // Cookie默认仅仅在https传输
    'secure'                =>  false,
    // Cookie默认httponly
    'httponly'              =>  false,
    // Cookie默认url转码 
    'transcoding'           =>  true,
    //默认Cookie值转义加密
    'encode'                =>  false,
    //仅仅允许获得默认前缀的cookie
    'filter'                =>  false,
];