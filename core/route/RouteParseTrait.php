<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;

trait RouteParseTrait
{
    // 解析信息数组
    // url格式 protocol ://  hostname[:port] / path / [;parameters][?query]#fragment
    // 因#fragment无法获取,所以忽略,[;parameters]这个不常用,忽略
    private static $parse_info = [
        // 'protocol' =>'',
        // 'domain'   =>'',
        // 'method'   =>'',
        // 'path'     =>'',
        // 'query'    =>'',
        // 'get'      =>[],
    ];

    // 解析路径和查询参数
    private static function parsePathAndQuery() : void
    {
        // 获取路径和get参数
        $path_and_query = urldecode(ltrim($_SERVER['REQUEST_URI'], '/'));

        // 若果不存在get参数,例:www.example.com/nihao/20
        if (false === $pos = strpos($path_and_query, '?')) {
            // 直接赋值
            static::$parse_info['path'] = static::deletePathSuffix($path_and_query);
            $_GET = static::$parse_info['get'] = [];
        } else {
            // 分割path和query
            static::$parse_info['path']  = static::deletePathSuffix(substr($path_and_query, 0, $pos));
            static::$parse_info['query'] = $query = substr($path_and_query, $pos + 1);
            !empty($query) && static::parseQuery($query);
        }

        static::$pending_path = explode('/', static::$parse_info['path']);
    }

    // 解析查询(get)参数
    private static function parseQuery(string $query) : void
    {
        // query语句解析
        array_map(function (string $param) {
            // 包括等于号,避免不完全的param参数
            if (false !== $pos = strpos($param, '=')) {
                // 添加到数组中
                static::$parse_info['get'][substr($param, 0, $pos)] = substr($param, $pos + 1);
            }
        }, explode('&', $query));

        $_GET = & static::$parse_info['get'];
    }

    // 移除'index.php','.php'等后缀
    private static function deletePathSuffix(string $path) : string
    {
        // 大于4个长度
        if (isset($path[4])) {
            // 如果倒数第四个字符为.,再判断是否以指定字符串结尾,是则移除,否则忽略
            if ('.' === $path[-4]) {
                if (in_array(substr($path, -10), ['/index.php', '/index.asp', '/index.jsp', '/index.jsf'])) {
                    $path = substr($path, 0, strlen($path)-10);
                } elseif (in_array(substr($path, -4), ['.php', '.asp', '.jsp', '.jsf'])) {
                    $path = substr($path, 0, strlen($path)-4);
                }
            // 倒数第五个字符串点,同上
            } elseif (isset($path[5]) && '.' === $path[-5]) {
                if (in_array(substr($path, -11), ['/index.html', '/index.aspx'])) {
                    $path = substr($path, 0, strlen($path)-11);
                } elseif (in_array(substr($path, -5), ['.html', '.aspx'])) {
                    $path = substr($path, 0, strlen($path)-5);
                }
            }
        }
        return trim($path, '/');
    }

    private static function getPath() : string
    {
        return static::$parse_info['path'];
    }
    // 获得查询语句(get参数)
    private static function getQuery() : string
    {
        return static::$parse_info['query'];
    }
    // 获得访问方法
    private static function getMethod() : string
    {
        return static::$parse_info['method'] = static::$parse_info['method'] ?? strtolower( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? 'ajax' : $_SERVER['REQUEST_METHOD'] );
    }
    // 获得协议
    private static function getProtocol() : string
    {
        if (!isset(static::$parse_info['protocol'])) {
            static::$parse_info['protocol'] = (isset($_SERVER['HTTPS']) && ('1' === $_SERVER['HTTPS'] || 'on' === strtolower($_SERVER['HTTPS'])))
            ||
            (isset($_SERVER['SERVER_PORT']) && '443' === $_SERVER['SERVER_PORT'])
            ? 'https'
            : 'http';
        }
        return static::$parse_info['protocol'];
    }
    // 获得域名
    private static function getDomain() : string
    {
        return static::$parse_info['domain'] = static::$parse_info['domain'] ?? $_SERVER['$_SERVER_NAME'] ?? $_SERVER['HTTP_HOST'];
    }
    // 获得端口
    private static function getPort() : int
    {
        return static::$parse_info['port'] = (int) static::$parse_info['port'] ?? $_SERVER['SERVER_PORT'];
    }
    // 获得ip
    private static function getIp() : string
    {
        return static::$parse_info['ip'] = static::$parse_info['ip'] ?? base\ip\Ip::get();
    }
    // 获得referer
    private static function getReferer() : string
    {
        return static::$parse_info['referer'] = static::$parse_info['referer'] ?? $_SERVER['HTTP_REFERER'];
    }
}