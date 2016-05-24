<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

class Route
{
    public static  $info          = [];
    public static  $group         = [];
    private static $params_handle = [];
    private static $namespace     = '\\app\\';
    private static $method        = '';
    private static $matched       = false;
    private static $roule         = [];


    public static function init()
    {
        //所有参数
        static::$params_handle = static::getParams();
        //判断当前方法
        static::$info['method'] = static::$info['method'] ?? strtolower(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? 'ajax' : $_SERVER['REQUEST_METHOD']);
    }
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


    public static function run()
    {
        try {
            require \msqphp\Environment::getPath('application').'route.php';
        } catch (RouteException $e) {
            throw new RouteException($e->getMessage());
        } catch(\Exception $e)
        {
            throw new RouteException($e->getMessage());
        }
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
     * 多语支持
     * @param array $info 语言信息
     * @return void
     */
    public static function addLanguage(array $info)
    {
        if (static::$matched) {
            return;
        }
        static::$info['language'] = static::getAllowedValue($info);
    }

    private static function getAllowedValue(array $info) : string
    {
        if (isset(static::$params_handle[0])) {
            $may = static::$params_handle[0];
            if (static::check($may, $info['allowed'])) {
                array_shift(static::$params_handle);
                return $may;
            } else {
                return $info['default'];
            }
        } else {
            return $info['default'];
        }
    }
    /**
     * 多主题支持
     * @param array $info 语言信息
     * @return void
     */
    public static function addTheme(array $info)
    {
        if (static::$matched) {
            return;
        }
        static::$info['theme'] = static::getAllowedValue($info);
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
        $group_name = static::getAllowedValue($group);
        //赋值给当前信息和分组, 键为组名, 值: 如果在允许范围内, 取其值, 否则取默认;
        static::$group[] = static::$group[$group['name']] = $group_name;

        //如果命名空间存在, 取其值
        if (isset($group['namespace'])) {
            if (is_bool($group['namespace'])) {
                $namespace = $group_name;
            } elseif (is_string($group['namespace'])) {
                $namespace = $group['namespace'];
            } else {
                throw new RouteException('未知的命名空间类型');
            }
            static::$namespace .= trim($namespace, '\\').'\\';
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
    /**
     * 是否是SSL协议, 即https
     * @return bool
     */
    public static function ssl($func, array $args = []) : bool
    {
        if (static::$matched) {
            return;
        }
        static::$info['ssl'] = static::$info['ssl'] ?? static::isSsl();
        if (static::$info['ssl']) {
            call_user_func_array($func, $args);
        }
    }
    private static function isSsl() : bool
    {
        if (isset($_SERVER['HTTPS']) && ('1' === $_SERVER['HTTPS'] || 'on' === strtolower($_SERVER['HTTPS']))) {
            return true;
        } elseif (isset($_SERVER['SERVER_PORT']) && '443' === $_SERVER['SERVER_PORT']) {
            return true;
        } else {
            return false;
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
    private static function checkRoule(string $value, string $roule) : bool
    {
        if (!isset(static::$roule[$roule])) {
            return false;
        }
        if (is_string(static::$roule[$roule])) {
            return 0 !== preg_match(static::$roule[$roule], $value);
        } else {
            return static::$roule[$roule]($value);
        }
    }
    private static function checkParam(array $params) : bool
    {
        $params_handle = static::$params_handle;
        $get = [];
        foreach ($params as $key => $value) {
            //遍历, 如果三等, 或者符合对应规则
            if ($value === $params_handle[$key] || static::checkRoule($params_handle[$key], $value)) {
                //$_GET重新生成;
                if ($key !== 0 && ($key % 2 === 0)) {
                    $get[$params[$key-1]] = $params_handle[$key];
                }
            //否则返回
            } else {
                return false;
            }
        }
        $_GET = $get;
        return true;
    }
    public static function get($param, $func, array $args = [])
    {
        static::method(['get'], $param, $func);
    }
    public static function ajax($param, $func, array $args = [])
    {
        static::method(['ajax'], $param, $func);
    }
    public static function post($param, $func, array $args = [])
    {
        static::method(['post'], $param, $func);
    }
    public static function method($method, $param, $func, array $args = [])
    {
        if (static::$matched) {
            return;
        }
        if (!in_array(static::$info['method'], (array)$method)) {
            return;
        }

        foreach ((array)$param as $value) {

            $params = explode('/', $value);

            if (count($params) !== count(static::$params_handle)) {
                return;
            }

            if (!static::checkParam($params)) {
                return;
            }
        }

        static::$matched = true;
        if (is_string($func)) {
            static::callUserClassFunc($func);
        } else {
            call_user_func_array($func, $args);
        }
    }
    private static function callUserClassFunc(string $func)
    {
        list($class , $method) = explode('@', $func);
        $get_pos = strpos($method, '?');
        $post_pos = strpos($method, '#');
        $args = [];
        if (false !== $get_pos && false !== $post_pos) {
            if ($get_pos > $post_pos) {
                list($method, $args_str) = explode('?', $method);
                list($get_args, $post_args) = explode('#', $args_str);
                foreach ($get_args as $key) {
                    if (!isset($_GET[$key])) {
                        throw new RouteException($key.' get值不存在');
                    } else {
                        $args[] = $_GET[$key];
                    }
                }
                foreach ($post_args as $key) {
                    if (!isset($_POST[$key])) {
                        throw new RouteException($key.' post值不存在');
                    } else {
                        $args[] = $_POST[$key];
                    }
                }
            } else {
                list($method, $args_str) = explode('#', $method);
                list($get_args, $post_args) = explode('?', $args_str);
                foreach ($post_args as $key) {
                    if (!isset($_POST[$key])) {
                        throw new RouteException($key.' post值不存在');
                    } else {
                        $args[] = $_POST[$key];
                    }
                }
                foreach ($get_args as $key) {
                    if (!isset($_GET[$key])) {
                        throw new RouteException($key.' get值不存在');
                    } else {
                        $args[] = $_GET[$key];
                    }
                }
            }
        } elseif (false !== $get_pos) {
            list($method, $get_args) = explode('?', $method);
            foreach ($get_args as $key) {
                if (!isset($_GET[$key])) {
                    throw new RouteException($key.' get值不存在');
                } else {
                    $args[] = $_GET[$key];
                }
            }
        } elseif (false !== $post_pos) {
            list($method, $post_args) = explode('#', $method);
            foreach ($post_args as $key) {
                if (!isset($_POST[$key])) {
                    throw new RouteException($key.' post值不存在');
                } else {
                    $args[] = $_POST[$key];
                }
            }
        }
        $class_name = static::$namespace . $class;
        $cont = new $class_name();
        call_user_func_array([$cont, $method], $args);
        unset($cont);
    }
    public static function error($func, array $args = [])
    {
        if (static::$matched) {
            return;
        }
        call_user_func_array($func, $args);
    }
}