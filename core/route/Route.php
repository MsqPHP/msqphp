<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

final class Route
{
    use traits\callStatic;

    public static  $info          = [];
    public static  $group         = [];
    private static $params_handle = [];
    private static $namespace     = '\\app\\';
    private static $matched       = false;
    private static $roule         = [];

    /**
     * 得到所有url参数
     *
     * @return array
     */
    private static function getParams() : array
    {
        //获得参数字符串
        $params_str = urldecode(trim($_SERVER['REQUEST_URI'] ?? $_SERVER['QUERY_STRING'], '/'));
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
        static::$info['method'] = static::$info['method'] ?? strtolower( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ? 'ajax' : $_SERVER['REQUEST_METHOD'] );

        try {
            //载入文件
            require \msqphp\Environment::getPath('application').'route.php';
        } catch (RouteException $e) {

            throw new RouteException($e->getMessage());

        } catch(\msqphp\core\exception\Exception $e) {

            throw new RouteException($e->getMessage());
        }
    }

###
#  添加信息
###
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
     *
     * @param  array $group 分组信息
     *
     * @example $group = [
     *          'name'      => string(组名),
     *          'alowed'    => string(路由规则)  |  array(允许值),
     *          'default'   => string(默认值),
     *          'namespace' => string(固定值)    |  true(与组值相同)(可选)
     *          ];
     *
     * @return void
     */
    public static function addGroup(array $group)
    {
        if (static::$matched) {
            return;
        }
        //赋值给当前信息和分组, 键为组名, 值: 如果在允许范围内, 取其值, 否则取默认;
        static::$group[] = static::$group[$group['name']] = $group_name = static::getAllowedValue($group);

        //如果命名空间存在, 取其值
        if (isset($group['namespace'])) {
            //bool等于组值
            if (is_bool($group['namespace'])) {
                $namespace = $group_name;
            } elseif (is_string($group['namespace'])) {
            //否则为过固定值
                $namespace = $group['namespace'];
            } else {
                throw new RouteException('未知的命名空间类型');
            }
            //添加到当前命名空间
            static::$namespace .= trim($namespace, '\\').'\\';
        }
    }
###
#  限制函数
###
    /**
     * 分组
     *
     * @param  string $group 组名
     * @param  string $value 组值
     * @param  func   $func  执行函数
     * @param  array  $args  函数参数
     *
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
    /**
     * 域名限制
     *
     * @param  miexd  $domain 域名 支持多
     * @param  func   $func  执行函数
     * @param  array  $args  函数参数
     *
     * @return void
     */
    public static function domain($domain, $func, array $args = [])
    {
        if (static::$matched) {
            return;
        }
        //获得当前域名
        static::$info['domain'] = static::$info['domain'] ?? $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'];

        if (in_array(static::$info['domain'], (array)$domain)) {
            call_user_func_array($func, $args);
        }
    }
    /**
     * ip限制
     *
     * @param  miexd  $ip    ip 支持多
     * @param  func   $func  执行函数
     * @param  array  $args  函数参数
     *
     * @return void
     */
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
     * SSL协议, 即https限制
     *
     * @param  func   $func  执行函数
     * @param  array  $args  函数参数
     *
     * @return void
     */
    public static function ssl($func, array $args = []) : void
    {
        if (static::$matched) {
            return;
        }

        static::$info['ssl'] = static::$info['ssl'] ?? static::isSsl();

        if (static::$info['ssl']) {
            call_user_func_array($func, $args);
        }
    }

    /**
     * 端口限制
     *
     * @param  miexd  $port  端口,支持多
     * @param  func   $func  执行函数
     * @param  array  $args  函数参数
     *
     * @return void
     */
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
###
#  方法函数
###
    public static function get($param, $func, array $args = [] , bool $autoload = false)
    {
        static::method(['get'], $param, $func, $args, $autoload);
    }
    public static function ajax($param, $func, array $args = [] , bool $autoload = false)
    {
        static::method(['ajax'], $param, $func, $args, $autoload);
    }
    public static function post($param, $func, array $args = [] , bool $autoload = false)
    {
        static::method(['post'], $param, $func, $args, $autoload);
    }
    public static function method($method, $param, $func, array $args = [], bool $autoload = false)
    {
        if (static::$matched) {
            return;
        }
        if (!in_array(static::$info['method'], (array)$method)) {
            return;
        }

        foreach ((array)$param as $value) {

            $params = explode('/', $value);

            if (count($params) === count(static::$params_handle) && static::checkParam($params)) {
                static::$matched = true;
                $matched = md5(implode('/',static::$group).$value);
                break;
            }

        }

        if (!static::$matched) {
            return;
        }

        if ($autoload && !defined('NO_CACHE')) {
            $cache = core\cache\Cache::getInstance()->init()->key($matched);
            if ($cache->exists() && false === \msqphp\Environment::$autoload_changed) {
                $autoload_info = $cache->get();
                if (!isset($autoload_info['begin']) && isset($autoload_info['last'])) {
                    array_map(function ($file) {
                        require $file;
                    }, $autoload_info['last']);
                } else {
                    $begin = [];
                    $middle = [];
                    foreach (array_reverse($autoload_info['begin']) as $file) {
                        if (in_array($file, \msqphp\Environment::$autoload_classes)) {
                            $begin[] = $file;
                        } else {
                            $middle[] = $file;
                            require $file;
                        }
                    }
                    if (isset($autoload_info['middle'])) {
                        foreach ($autoload_info['middle'] as $file) {
                            $middle[] = $file;
                            require $file;
                        }
                    }

                    if (empty($begin)) {
                        $autoload_info = ['last'=>$middle];
                    } else {
                        $autoload_info = ['begin'=>$begin,'middle'=>$middle];
                    }

                    $autoload_changed = true;
                }
            } else {
                $autoload_info = [];
            }
        }

        if (is_string($func)) {
            static::callUserClassFunc($func);
        } else {
            call_user_func_array($func, $args);
        }
        if ($autoload && !defined('NO_CACHE')) {
            if (!empty(\msqphp\Environment::$autoload_classes)) {
                $autoload_info['begin'] = array_merge($autoload_info['begin'] ?? [], \msqphp\Environment::$autoload_classes);
                \msqphp\Environment::$autoload_classes = [];
                $autoload_changed = true;
            }
            if ($autoload_changed) {
                if (isset($autoload_info['begin']) ) {
                    $autoload_info['begin'] = array_unique($autoload_info['begin']);
                }
                if (isset($autoload_info['middle'])) {
                  $autoload_info['middle'] = array_unique($autoload_info['middle']);
                }
                core\cache\Cache::getInstance()->init()->key($matched)->value($autoload_info)->set();
            } elseif (rand(0,5000) === 1000) {
                core\cache\Cache::getInstance()->init()->key($matched)->delete();
            }
        }
    }

    public static function error($func, array $args = [])
    {
        if (static::$matched) {
            return;
        }
        call_user_func_array($func, $args);
    }
###
#  私有函数
###
    /**
     * 得到允许值,用于addLanguage,addTheme,addGroup
     *
     * @param  array  $info 信息['alowed'=>string(路由规则)|array(指定值),'default',默认值];
     *
     * @return string
     */
    private static function getAllowedValue(array $info) : string
    {
        //如果当前url参数仍有值 并且 检测成功
        if (isset(static::$params_handle[0]) && static::check(static::$params_handle[0], $info['allowed'])) {
            //取值并删除
            array_shift(static::$params_handle);
            return static::$params_handle[0];
        } else {
            //取默认
            return $info['default'];
        }
    }
    /**
     * 规则检测
     *
     * @param  string $may   可能值
     * @param  string $value 路由规则键
     * @param  array  $value 指定值
     *
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
     * 是否是ssl协议
     *
     * @return bool
     */
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
    /**
     * 检测参数是否符合并重组$_GET;
     *
     * @param  array  $params 待检测参数
     *
     * @return bool
     */
    private static function checkParam(array $params) : bool
    {
        $params_handle = static::$params_handle;
        $get = [];
        foreach ($params as $key => $value) {
            //遍历, 如果三等, 或者符合对应规则
            if ($value === $params_handle[$key] || isset(static::$roule[$value]) && static::checkRoule($params_handle[$key], $value)) {
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
    /**
     * 调用用户类方法,并传递对应值
     *
     * @param  string $func class@method?get1#post2
     *
     * @return void
     */
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
                foreach (explode('&', $get_args) as $key) {
                    if (!isset($_GET[$key])) {
                        throw new RouteException($key.' get值不存在');
                    } else {
                        $args[] = $_GET[$key];
                    }
                }
                foreach (explode('&', $post_args) as $key) {
                    if (!isset($_POST[$key])) {
                        throw new RouteException($key.' post值不存在');
                    } else {
                        $args[] = $_POST[$key];
                    }
                }
            } else {
                list($method, $args_str) = explode('#', $method);
                list($get_args, $post_args) = explode('?', $args_str);
                foreach (explode('&', $post_args) as $key) {
                    if (!isset($_POST[$key])) {
                        throw new RouteException($key.' post值不存在');
                    } else {
                        $args[] = $_POST[$key];
                    }
                }
                foreach (explode('&', $get_args) as $key) {
                    if (!isset($_GET[$key])) {
                        throw new RouteException($key.' get值不存在');
                    } else {
                        $args[] = $_GET[$key];
                    }
                }
            }
        } elseif (false !== $get_pos) {
            list($method, $get_args) = explode('?', $method);
            foreach (explode('&', $get_args) as $key) {
                if (!isset($_GET[$key])) {
                    throw new RouteException($key.' get值不存在');
                } else {
                    $args[] = $_GET[$key];
                }
            }
        } elseif (false !== $post_pos) {
            list($method, $post_args) = explode('#', $method);
            foreach (explode('&', $post_args) as $key) {
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
}