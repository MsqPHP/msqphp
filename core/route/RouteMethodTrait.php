<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\core;

trait RouteMethodTrait
{
    //get
    public static function get($params, $func, array $args = [] , bool $autoload = false)
    {
        static::method(['get'], $params, $func, $args, $autoload);
    }

    //ajax
    public static function ajax($params, $func, array $args = [] , bool $autoload = false)
    {
        static::method(['ajax'], $params, $func, $args, $autoload);
    }

    //post
    public static function post($params, $func, array $args = [] , bool $autoload = false)
    {
        static::method(['post'], $params, $func, $args, $autoload);
    }

    /**
     * 方法调用
     *
     * @param  array        $method   匹配方法
     * @param  miexd        $params    匹配参数,支持多
     * @param  miexd        $func     调用方法
     * @param  array        $args     传递参数
     * @param  bool         $autoload 智能加载
     *
     * @return void
     */
    public static function method(array $method, $params, $func, array $args = [], bool $autoload = false)
    {
        if (static::$matched) {
            return;
        }
        if (!in_array(static::$info['method'], $method)) {
            return;
        }

        foreach ((array)$params as $param) {
            if (static::checkParam(explode('/', $param))) {
                static::$matched = true;
                $autoload && $matched_key = md5(implode('/',static::$group).$param);
                break;
            }
        }

        if (!static::$matched) {
            return;
        }

        unset($method);
        unset($params);

        if ($autoload && !defined('NO_CACHE')) {
            $aiload = core\aiload\AiLoad::getInstance()->init()->key($matched_key);
            if (\msqphp\Environment::$autoload_changed) {
                $aiload->delete();
            }
            $aiload->load();
        }

        unset($matched_key);

        if (is_string($func)) {
            static::callUserClassFunc($func);
        } else {
            call_user_func_array($func, $args);
        }
        if ($autoload && !defined('NO_CACHE')) {
            if ($aiload->changed()) {
                $aiload->update()->save()->end();
            } else {
                rand(0,5000) === 1000 && $aiload->delete();
                $aiload->end();
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

        if (count($params) !== count(static::$params_handle)) {
            return false;
        }

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
        $args = [];
        $get_pos = strpos($method, '?');
        $post_pos = strpos($method, '#');

        if (false !== $get_pos && false !== $post_pos) {
            if ($get_pos > $post_pos) {
                list($method, $args_str) = explode('?', $method);
                list($get_args, $post_args) = explode('#', $args_str);
                static::addPostArgs($post_args, $args);
                static::addGetArgs($add_args, $args);
            } else {
                list($method, $args_str) = explode('#', $method);
                list($get_args, $post_args) = explode('?', $args_str);
                static::addGetArgs($add_args, $args);
                static::addPostArgs($post_args, $args);
            }
            unset($get_args);
            unset($post_args);
            unset($args_str);
        } elseif (false !== $get_pos) {
            list($method, $get_args) = explode('?', $method);
            static::addGetArgs($add_args, $args);
            unset($get_args);
        } elseif (false !== $post_pos) {
            list($method, $post_args) = explode('#', $method);
            static::addPostArgs($post_args, $args);
            unset($post_args);
        }
        unset($get_pos);
        unset($post_pos);

        $class_name = static::$namespace . $class;

        $cont = new $class_name();
        call_user_func_array([$cont, $method], $args);
        unset($cont);
    }


    private static function addPostArgs(string $post_args, array & $args)
    {
        foreach (explode('&', $post_args) as $key) {
            if (!isset($_POST[$key])) {
                throw new RouteException($key.' post值不存在');
            } else {
                $args[] = $_POST[$key];
            }
        }
    }
    private static function addGetArgs(string $get_args, array & $args)
    {
        foreach (explode('&', $get_args) as $key) {
            if (!isset($_GET[$key])) {
                throw new RouteException($key.' get值不存在');
            } else {
                $args[] = $_GET[$key];
            }
        }
    }
}