<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\core;

trait RouteMethodTrait
{
    /**
     * @param array    $method    get\post\ajax方法(匹配多);
     *
     * @param string   $param     匹配单个
     * @param array   $param     匹配多个
     *
     * @param string   $func      调用对应类对应方法
     * @param \Closure $func      调用函数,并传参$args
     *
     * @param bool     $aiload    是否智能加载需要类文件(默认否,在一个类方法或函数加载类文件过多的时候使用)
     *
     * @return void
     */

    //get
    public static function get($params, $func, array $args = [] , bool $autoload = false)
    {
        static::$matched || static::method(['get'], $params, $func, $args, $autoload);
    }

    //ajax
    public static function ajax($params, $func, array $args = [] , bool $autoload = false)
    {
        static::$matched || static::method(['ajax'], $params, $func, $args, $autoload);
    }

    //post
    public static function post($params, $func, array $args = [] , bool $autoload = false)
    {
        static::$matched || static::method(['post'], $params, $func, $args, $autoload);
    }

    //method
    public static function method(array $method, $params, $func, array $args = [], bool $autoload = false)
    {
        if (static::$matched) {
            return;
        }


        if (!in_array(static::$info['method'], $method)) {
            return;
        }

        foreach ((array)$params as $param) {

            if (static::checkParam($param)) {
                continue;
            }

            static::$matched = true;

            if ($autoload) {

                $aiload = core\aiload\AiLoad::getInstance()->init()->key(md5(static::$url.implode('/', (array)$params).'/'.implode('/', $method)));

                \msqphp\Environment::$autoload_changed && $aiload->delete();

                $aiload->load();
            }

            if (is_string($func)) {

                static::callUserClassFunc($func, $args);

            } elseif ($func instanceof \Closure) {

                call_user_func_array($func, $args);

            } else {
                throw new RouteException('错误的回调函数');
            }

            if ($autoload) {

                if ($aiload->changed()) {
                    $aiload->update()->save()->end();
                } else {
                    rand(0,5000) === 1000 && $aiload->delete();
                    $aiload->end();
                }
            }

        }

    }

    public static function error(\Closure $func, array $args = [])
    {
        static::$matched || call_user_func_array($func, $args);
    }
    /**
     * 检测参数是否符合并重组$_GET;
     *
     * @param  array  $params 待检测参数
     *
     * @return bool
     */
    private static function checkParam(string $params) : bool
    {
        $params = explode('/', $params);

        $params_handle = static::$params_handle ?: [''];

        if (count($params) !== count($params_handle)) {
            return false;
        }

        $get = [];

        foreach ($params as $key => $value) {
            //遍历, 如果三等, 或者符合对应规则
            if ($value === $params_handle[$key] || static::checkRoule($params_handle[$key], $value)) {
                //$_GET重新生成;
                //忽略第0个参数,当为偶数时 赋 上一个单数为键, 当前偶数为值
                $key !== 0 && ($key % 2 === 0) && $get[$params[$key-1]] = $params_handle[$key];
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
    private static function callUserClassFunc(string $func, array $args)
    {
        list($class , $method) = explode('@', $func, 2);
        list($method, $args) = static::getArgs($method, $args);
        $class_name = static::$namespace . $class;
        call_user_func_array([new $class_name(), $method], $args);
    }
    private static function getArgs(string $param, array $args) : array
    {
        $get_pos = strpos($param, '?');
        $post_pos = strpos($param, '#');
        if (false !== $get_pos && false !== $post_pos) {
            if ($get_pos > $post_pos) {
                list($method, $args_str) = explode('?', $param, 2);
                list($get_param, $post_param) = explode('#', $args_str, 2);
                static::addArgs($post_param, $args, $_POST);
                static::addArgs($get_param, $args, $_GET);
            } else {
                list($method, $args_str) = explode('#', $param, 2);
                list($get_param, $post_param) = explode('?', $args_str, 2);
                static::addArgs($get_param, $args, $_GET);
                static::addArgs($post_param, $args, $_POST);
            }
        } elseif (false !== $get_pos) {
            list($method, $get_param) = explode('?', $param);
            static::addArgs($get_param, $args, $_GET);
        } elseif (false !== $post_pos) {
            list($method, $post_param) = explode('#', $param);
            static::addArgs($post_param, $args, $_POST);
        } else {
            $method = $param;
        }
        return [$method, $args];
    }
    private static function addArgs(string $add_args, array & $args, array $target)
    {
        foreach (explode('&', $add_args) as $key) {
            if (!isset($target[$key])) {
                throw new RouteException($key.' get值不存在');
            } else {
                $args[] = $target[$key];
            }
        }
    }
}