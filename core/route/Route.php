<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\traits;

final class Route
{
    //万能静态call
    use traits\CallStatic;

    //路由规则trait
    use RouteRouleTrait;

    //路由组trait
    use RouteGroupTrait;

    //路由限制类型trait
    use RouteLimiteTrait;

    //路由方法类型trait
    use RouteMethodTrait;

    //保存各种信息的数组
    public static  $info          = [];

    //当前处理的url参数数组
    private static $params_handle = [];

    //当前处理的url
    private static $url           = '';
    //当前命名空间
    private static $namespace     = '\\app\\';

    //是否匹配成功过
    private static $matched       = false;


    /**
     * route运行
     *
     * @return void
     */
    public static function run()
    {
        //初始化
        static::init();

        //路由流程文件
        $file = \msqphp\Environment::getPath('application').'route.php';

        if (!is_file($file)) {
            throw new RouteException('路由解析失败,原因:'.(string)$file.'流程文件');
        }

        //载入文件
        require $file;
    }

    /**
     * 构建并获取url常量
     *
     * @return string
     */
    public static function bulid() : string
    {
        defined('__URL__') || define('__URL__', static::$url);
        return static::$url;
    }

    /**
     * 路由初始化
     *
     * @return void
     */
    private static function init()
    {
        $server = & $_SERVER;


        $info                  = [];

        //是否htpps
        $info['ssl']           =
        (isset($server['HTTPS']) && ('1' === $server['HTTPS'] || 'on' === strtolower($server['HTTPS'])))
        ||
        (isset($server['SERVER_PORT']) && '443' === $server['SERVER_PORT']);

        //域名
        $info['domain']        = $server['SERVER_NAME'] ?? $server['HTTP_HOST'];

        //url赋值
        static::$url           = ($info['ssl'] ? 'https://' : 'http://') . $info['domain'] . '/';

        //当前方法
        $info['method']        = strtolower( isset($server['HTTP_X_REQUESTED_WITH']) && strtolower($server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? 'ajax' : $server['REQUEST_METHOD'] );

        //赋值
        static::$info          = $info;

        //获得所有参数(一维数组)
        static::$params_handle = static::getParams(urldecode(ltrim($_SERVER['REQUEST_URI'], '/')));

        unset($server);
    }

    /**
     * 得到所有url参数
     *
     * @example www.example.com?page=20&name=test ----> ['page','20','name','test']
     * @example www.example.com/page/20/name/test ----> ['page','20','name','test']
     * @example www.example.com/page/20?name=test ----> ['page','20','name','test']
     *
     * @return array
     */
    private static function getParams(string $url) : array
    {

        //非get解析,只是将此时url的get参数转换为1维数组
        $get = [];

        //若果不存在? 例:www.example.com/page/20/name/test
        if (false === $pos = strpos($url, '?')) {

            //直接赋值
            $request = $url;

        } else {

        //或者www.example.com?page=20&name=test

            //取?以前
            $request = substr($url, 0, $pos);

            //?以后解析为$get
            array_map(function (string $param) use (& $get) {

                //包括等于号,避免不完全的get参数
                if (false !== $pos = strpos($param, '=')) {
                    //添加到数组中,  等于号前                 等于号后
                    array_push($get, substr($param, 0, $pos), substr($param, $pos + 1));
                }

            //?以后以&分割
            }, explode('&', substr($url, $pos + 1)));

        }

        unset($url);

        //后缀去除
        if (false !== strpos($request, '.')) {
            if (in_array(substr($request, -11), ['/server.php', '/index.html', '/index.aspx'])) {
                $request = substr($request, 0, strlen($request)-11);
            } elseif (in_array(substr($request, -10), ['/index.php', '/index.asp', '/index.jsp', '/index.jsf'])) {
                $request = substr($request, 0, strlen($request)-10);
            } elseif (in_array(substr($request, -5), ['.html', '.aspx'])) {
                $request = substr($request, 0, strlen($request)-5);
            } elseif (in_array(substr($request, -4), ['.php', '.asp', '.jsp', '.jsf'])) {
                $request = substr($request, 0, strlen($request)-4);
            }
        }

        //赋值request,请求参数(不带get)
        static::$info['request'] = $request;

        return array_merge(explode('/', $request), $get);
    }
}