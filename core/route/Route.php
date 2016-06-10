<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\traits;

final class Route
{
    use traits\callStatic;

    use RouteLimiteTrait;

    use RouteMethodTrait;

    public static  $info          = [];
    public static  $group         = [];
    private static $params_handle = [];
    private static $namespace     = '\\app\\';
    private static $roule         = [];
    private static $matched       = false;
    /**
     * route运行
     *
     * @return void
     */
    public static function run()
    {
        //获得所有参数(一维数组)
        static::$info['params'] = static::$params_handle = static::getParams();
        //判断当前方法
        static::$info['method'] = strtolower( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? 'ajax' : $_SERVER['REQUEST_METHOD'] );

        $file = \msqphp\Environment::getPath('application').'route.php';
        if (!is_file($file)) {
            throw new RouteException($file.'不存在');
        }
        try {
            //载入文件
            require $file;
        } catch (RouteException $e) {

            throw new RouteException($e->getMessage());
        }
    }
    /**
     * 得到所有url参数
     *
     * @return array
     */
    private static function getParams() : array
    {
        //获得参数字符串
        $uri = urldecode(trim($_SERVER['REQUEST_URI'] ?? $_SERVER['QUERY_STRING'], '/'));
        $right = [];
        //若果不存在? 例:www.example.com/page/20/name/test
        if (false === $pos = strpos($uri, '?')) {
            $left_uri = $uri;
        } else {
        //或者www.example.com?page=20&name=test
            $left_uri = substr($uri, 0, $pos);
            array_map(function($param) use (& $right) {
                list($k, $v) = explode('=', $param);
                if (false !== $pos = strpos($param, '=')) {
                    array_push($right, substr($param, 0, $pos), substr($param, $pos + 1));
                }
            }, explode('&', substr($uri, $pos + 1)));
        }
        //后缀去除
        if (in_array(substr($left_uri, -11), ['/server.php', '/index.html', '/index.aspx'])) {
            $left_uri = substr($left_uri, 0, strlen($left_uri)-11);
        } elseif (in_array(substr($left_uri, -10), ['/index.php', '/index.asp', '/index.jsp', '/index.jsf'])) {
            $left_uri = substr($left_uri, 0, strlen($left_uri)-10);
        } elseif (in_array(substr($left_uri, -5), ['.html', '.aspx'])) {
            $left_uri = substr($left_uri, 0, strlen($left_uri)-5);
        } elseif (in_array(substr($left_uri, -4), ['.php', '.asp', '.jsp', '.jsf'])) {
            $left_uri = substr($left_uri, 0, strlen($left_uri)-4);
        }

        return array_merge(explode('/', $left_uri), $right);
    }
    /**
     * 规则检测
     *
     * @param  string $may   可能值
     * @param  array  $value 指定值
     *
     * @throws RouteException
     * @return bool
     */
    private static function check(string $may, $value) : bool
    {
        //如果是个字符串, 表明调用对应规则
        if (is_string($value)) {
            return static::checkRoule($may, $value);
        } elseif (is_array($value)) {
        //如果是个数组, 判断是否是数组中的某个值
            return in_array($may, $value);
        } else {
            throw new RouteException($may.'未知的检测类型'.$value);
        }
    }
    /**
     * 路由规则检测
     *
     * @param  string $value 值
     * @param  string $roule 规则键
     *
     * @throws RouteException
     * @return bool
     */
    private static function checkRoule(string $value, string $roule) : bool
    {
        if (!isset(static::$roule[$roule])) {
            throw new RouteException('路由规则不存在');
        }
        if (is_string(static::$roule[$roule])) {
            return 0 !== preg_match(static::$roule[$roule], $value);
        } else {
            return static::$roule[$roule]($value);
        }
    }
}