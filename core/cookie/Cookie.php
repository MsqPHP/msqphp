<?php declare(strict_types = 1);
namespace msqphp\core\cookie;

use msqphp\core;
use msqphp\traits;


final class Cookie
{
    //单例trait
    use traits\Instance;
    //指针trait
    use CookiePointerTrait;
    //操作trait
    use CookieOperateTrait;

    //当前编辑的cookie
    private $pointer          = [];

    //配置
    private $config   = [
        //前缀
        'prefix'      =>'',
        //过期时间
        'expire'      =>3600,
        //路径
        'path'        =>'/',
        //域名
        'domain'      =>'',
        //https
        'secure'      =>false,
        //httpoly
        'httponly'    =>false,
        //过滤
        'filter'      =>false,
        //url转义
        'transcoding' =>false,
        //加密
        'encode'      =>false,
    ];
    //当前脚本所有的cookie
    private $cookies   = [];

    /**
     * 构建函数
     */
    private function __construct()
    {
        $this->config = $config = array_merge($this->config, core\config\Config::get('cookie'));
        //是否过滤cookie
        if ($config['filter']) {
            $prefix  = $config['prefix'];
            $len     = strlen($prefix);
            $_COOKIE = array_filter($_COOKIE, function($key) use ($len, $prefix) {
                    return substr($key, 0, $len) === $prefix;
            }, ARRAY_FILTER_USE_KEY);
        }
        $this->cookies = & $_COOKIE;
    }
}