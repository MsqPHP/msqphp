<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;
use msqphp\core;

class Route
{
    public static  $info          = [];
    public static  $group         = [];
    private static $params_handle = [];
    private static $namespace     = '\\app\\';
    private static $method        = '';
    private static $matched       = false;
    private static $roule         = [];


    private static function getParams() : array
    {
        //获得参数字符串
        $params_str = urldecode(ltrim($_SERVER['REQUEST_URI'] ?? $_SERVER['QUERY_STRING'], '/'));
        //后缀去除
        if (in_array(substr($params_str, -11), ['/server.php', '/index.html', '/index.aspx'])) {
            $params_str = substr($params_str, 0, strlen($params_str)-11);
        } elseif (in_array(substr($params_str, -10), ['/index.php', '/index.asp', '/index.jsp', '/index.jsf'])) {
            $params_str = substr($params_str, 0, strlen($params_str)-10);
        } elseif (in_array(substr($params_str, -5), ['.html', '.aspx'])) {
            $params_str = substr($params_str, 0, strlen($params_str)-5);
        } elseif (in_array(substr($params_str, -4), ['.php', '.asp', '.jsp', '.jsf'])) {
            $params_str = substr($params_str, 0, strlen($params_str)-4);
        }
        return explode('/', trim(strtr($params_str, '=&?', '///'), '/'));
    }

    public static function parseUrl()
    {
        //所有参数
        static::$params_handle = static::getParams();
        //判断当前方法
        static::$method = strtolower(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? 'ajax' : $_SERVER['REQUEST_METHOD']);
    }

    public static function run()
    {
        define('PHP_CONT_START', microtime(true));

        try {
            require \msqphp\Environment::getPath('application').'route.php';
        } catch (RouteException $e) {
            throw new RouteException($e->getMessage());
        }

        define('PHP_CONT_END', microtime(true));
    }
    /**
     * 添加一条规则
     * @param  string $key  规则键
     * @param  func   $func 回调函数
     * @return void
     */
    public static function addRoule(string $key, $func)
    {
        static::$roule[$key] = $func;
    }
    /**
     * 多余支持
     * @param array $info 语言信息
     * @return void
     */
    public static function addLanguage(array $info)
    {
        if (static::$matched) {
            return;
        }
        if (isset(static::$params_handle[0])) {
            $may = static::$params_handle[0];
            if (static::check($may, $info['allowed'])) {
                $value = $may;
                array_shift(static::$params_handle);
            }
        }
        static::$info['language'] = $value ?? $info['default'];
    }
    /**
     * 增加一个url参数信息, 将获取第一个参数, 如果在列表中, 则取值, 删除, 否则取默认值
     * @param  array $group 分组信息
     * @return void
     */
    public static function addGroup(array $group)
    {
        if (static::$matched) {
            return;
        }
        //是否还有待处理的参数
        if (isset(static::$params_handle[0])) {
            $may = static::$params_handle[0];
            if (static::check($may, $group['allowed'])) {
                $value = $may;
                array_shift(static::$params_handle);
            }
        }

        //赋值给当前信息和分组, 键为组名, 值: 如果在允许范围内, 取其值, 否则取默认;
        static::$group[] = static::$group[$group['name']] = $value ?? $group['default'];

        //如果命名空间存在, 取其值
        if (isset($group['namespace'])) {
            if (is_bool($group['namespace'])) {
                $namespace = $value ?? $group['default'];
            } elseif (is_string($group['namespace'])) {
                $namespace = $group['namespace'];
            } else {
                throw new RouteException('未知的命名空间类型');
            }
            static::$namespace .= trim($namespace, '\\').'\\';
        }
    }
    private static function check(string $may, $value) : bool
    {
        //如果是个字符串, 表明调用对应规则
        if (is_string($value)) {
            if (isset(static::$roule[$value])) {
                return static::$roule[$value]($may);
            } else {
                throw new RouteException($value.'路由规则不存在');
            }
        } elseif (is_array($value)) {
        //如果是个数组, 判断是否是数组中的某个值
            return in_array($may, $value);
        } else {
            throw new RouteException($may.'未知的检测类型'.$value);
        }
    }
    /**
     * 分组
     * @param  string $group 组名
     * @param  string $value 组值
     * @param  func   $func  执行函数
     * @param  array  $args  函数参数
     * @return void
     */
    public static function group(string $group, string $value, $func , array $args = [])
    {
        if (static::$matched) {
            return;
        }
        if (static::$group[$group] === $value) {
            call_user_func_array($func, $args);
        }
    }
    public static function domain($domain, $func, array $args = [])
    {
        if (static::$matched) {
            return;
        }
        //当前域名
        static::$info['domain'] = static::$info['domain'] ?? $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'];
        if (in_array(static::$info['domain'], (array)$domain)) {
            call_user_func_array($func, $args);
        }
    }
    public static function ip( $ip, $func, array $args = [])
    {
        if (static::$matched) {
            return;
        }
        static::$info['ip'] = static::$info['ip'] ?? base\ip\Ip::get();
        if (in_array(static::$info['ip'], (array)$ip)) {
            call_user_func_array($func, $args);
        }
    }
    public static function port( $port, $func, array $args = [])
    {
        if (static::$matched) {
            return;
        }
        static::$info['port'] = static::$info['port'] ?? $_SERVER['SERVER_PORT'];
        if (in_array(static::$info['port'], (array)$port)) {
            call_user_func_array($func, $args);
        }
    }
    public static function get(string $param, $func)
    {
        if (static::$matched || 'get' !== static::$method) {
            return;
        }
        $params = explode('/', $param);

        $params_handle = static::$params_handle;
        if (count($params) !== count($params_handle)) {
            return;
        } else {
            $get = [];
            foreach ($params as $key => $value) {
                //遍历, 如果三等, 或者符合对应规则
                if ($value === $params_handle[$key] || isset(static::$roule[$value]) && static::$roule[$value]($params_handle[$key])) {
                    //$_GET重新生成;
                    if ($key !== 0 && ($key % 2 === 0)) {
                        $_GET[$params[$key-1]] = $params_handle[$key];
                        $get[] = $params_handle[$key];
                    }
                //否则返回
                } else {
                    return;
                }
            }
            static::$matched = true;
            if (is_string($func)) {
                list($class , $method) = explode('@', $func);
                if (false !== strpos($method, '?')) {
                    list($method, $arg) = explode('?', $method);
                    $args = explode('&', $arg);
                    $args = array_map(
                        function($key) {
                            return $_GET[$key];
                        },
                        $args
                    );
                } else {
                    $args = [];
                }

                $namespace = static::$namespace . $class;
                $cont = new $namespace();
                call_user_func_array([$cont, $method], $args);
                unset($cont);
            } else {
                call_user_func_array($func, $get);
            }
        }
    }
    public static function ajax(string $param, $func)
    {
        if (static::$matched || 'ajax' !== static::$method) {
            return;
        }

        if ($param !== static::$module[0]) {
            return;
        } else {
            static::$matched = true;
            call_user_func_array($func, []);
        }
    }
    public static function post(string $param, $func)
    {
        if (static::$matched || 'post' !== static::$method) {
            return;
        }

        $params = explode('/', $param);

        $params_handle = static::$params_handle ?: [0=>''];

        if (count($params) === count($params_handle)) {
            $get = [];
            foreach ($params as $key => $value) {
                //遍历, 如果三等, 或者符合对应规则
                if ($value === $params_handle[$key] || isset(static::$roule[$value]) && static::$roule[$value]($params_handle[$key])) {
                    //$_GET重新生成;
                    if ($key !== 0 && ($key % 2 === 0)) {
                        $_GET[$params[$key-1]] = $params_handle[$key];
                        $get[] = $params_handle[$key];
                    }
                //否则返回
                } else {
                    return;
                }
            }
            static::$matched = true;
            if (is_string($func)) {
                list($class , $method) = explode('@', $func);
                $args = [];
                if (false !== strpos($method, '?')) {
                    list($method, $arg) = explode('?', $method);
                    $get = explode('&', $arg);
                    foreach ($get as $key) {
                        $args[] = $_GET[$key];
                    }
                }
                if (false !== strpos($method, '#')) {
                    list($method, $arg) = explode('#', $method);
                    $post = explode('&', $arg);
                    foreach ($post as $key) {
                        $args[] = $_POST[$key];
                    }
                }
                $namespace = static::$namespace . $class;
                $cont = new $namespace();
                call_user_func_array([$cont, $method], $args);
                unset($cont);
            } else {
                call_user_func_array($func);
            }
        } else {
            return;
        }
    }
    public static function error($func, array $args = [])
    {
        if (static::$matched) {
            return;
        }
        call_user_func_array($func, $args);
    }
    /**
     * 是否是SSL协议, 即https
     * @return bool
     */
    public static function isSsl() : bool
    {
        return
        isset($_SERVER['HTTPS']) && ('1' === $_SERVER['HTTPS'] || 'on' === strtolower($_SERVER['HTTPS']))
        ||
        isset($_SERVER['SERVER_PORT']) && '443' === $_SERVER['SERVER_PORT'];
    }
}