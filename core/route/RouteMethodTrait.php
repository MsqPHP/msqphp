<?php declare(strict_types = 1);
namespace msqphp\core\route;

trait RouteMethodTrait
{
    private static $method_info = [];
    use RouteMethodRunTrait;
    use RouteMethodCheckTrait;

    /**
     * @param array    $method    get\post\ajax方法(匹配多);
     *
     * @param string   $conditions     匹配单个条件
     * @param array    $conditions     匹配多个条件
     *
     * @param string   $func      调用对应类对应方法
     * @param \Closure $func      调用函数,并传参$args
     *
     * @param bool     $aiload    是否智能加载需要类文件(默认否,在一个类方法或函数加载类文件过多的时候使用)
     *
     * @return void
     */


    // 错误,即匹配失败
    public static function error(\Closure $func, array $args = []) : void
    {
        static::$matched || call_user_func_array($func, $args);
    }

    // get
    public static function get($conditions, $func, array $args = [] , bool $aiload = false) : void
    {
        static::$matched || static::method(['get'], $conditions, $func, $args, $aiload);
    }

    // ajax
    public static function ajax($conditions, $func, array $args = [] , bool $aiload = false) : void
    {
        static::$matched || static::method(['ajax'], $conditions, $func, $args, $aiload);
    }

    // post
    public static function post($conditions, $func, array $args = [] , bool $aiload = false) : void
    {
        static::$matched || static::method(['post'], $conditions, $func, $args, $aiload);
    }

    // method
    public static function method(array $method, $conditions, $func, array $args = [], bool $aiload = false) : void
    {
        // 匹配成功,返回
        if (static::$matched) {
            return;
        }

        // 方法不匹配,返回
        if (!in_array(static::getMethod(), $method)) {
            return;
        }

        // 遍历参数,
        foreach ((array)$conditions as $condition) {
            if (static::checkCondition($condition)) {
                // 匹配成功
                static::$matched = true;
                static::$method_info = ['method' => $method, 'condition' => $conditions];
                unset($method, $conditions);
                static::callFunction($func, $args, $aiload);
                return;
            }
        }
    }


    private static function getArgsInfoByQuery(string $query) : array
    {
        if ($query[0] === '?') {
            $delimiter_else = '#';
            $source         = $_GET;
        } elseif ($query[0] === '#') {
            $delimiter_else = '?';
            $source         = $_POST;
        } else {
            static::exception('route匹配解析时出错,错误的url查询(get参数)数据');
        }

        // ?get&get2#post&post2?get3#post3
        // 如果还有其他请求类型参数,取其前
        if (false !== $pos = strpos($query, $delimiter_else, 1)) {
            $args_name = explode('&', substr($query, 1, $pos -1));
            $query = substr($query, $pos);
        } else {
        // 直接打散,清空;
            $args_name = explode('&', substr($query, 1));
            $query = '';
        }

        return [$query, $args_name, $source];
    }
    private static function getArgsByQuery(string $query, array $args) : array
    {
        while (isset($query[0])) {
            [$query, $args_name, $source] = static::getArgsInfoByQuery($query);

            foreach ($args_name as $arg_name) {
                $args[] = $source[$arg_name] ?? null;
            }
        }
        return $args;
    }
}

trait RouteMethodRunTrait
{
    /**
     * 执行函数
     *
     * @param string   $func      调用对应类对应方法
     * @param \Closure $func      调用函数,并传参$args
     *
     * @param bool     $aiload    是否智能加载需要类文件(默认否,在一个类方法或函数加载类文件过多的时候使用)
     *
     * @return void
     */
    private static function callFunction($func, array $args = [], bool $aiload) : void
    {
        if ($aiload) {
            $loader = app()->loader;
            $loader->key(md5(static::$url.var_export(static::$method_info,true)))->load();
        }

        // 字符串
        if (is_string($func)) {
            static::callUserClassMethod($func, $args);
        // 闭包函数
        } elseif ($func instanceof \Closure) {
            call_user_func_array($func, $args);
        } else {
            static::exception('错误的回调函数');
        }

        if ($aiload) {
            if ($loader->last()) {
                random_int(1, 1000) === 1000 && $loader->delete();
            } else {
                $loader->update();
            }
            unset($loader);
        }
    }

    /**
     * 调用用户类方法,并传递对应值
     *
     * @param  string $func class@method@?get1#post2
     *
     * @return void
     */
    private static function callUserClassMethod(string $func, array $args) : void
    {
        [$class , $method] = explode('@', $func, 2);

        if (false !== strpos($method, '@')) {
            [$method, $query] = explode('@', $method, 2);
        } else {
            $query = '';
        }
        $class_name = static::$namespace . $class;

        static::$method_info['function'] = [
            'class'  => $class_name,
            'method' => $method,
            'query'  => $query,
            'args'   => $args,
        ];

        call_user_func_array([new $class_name, $method], static::getArgsByQuery($query, $args));
    }
}

trait RouteMethodCheckTrait
{
    /**
     * 检测参数是否符合并重组$_GET;
     *
     * @param  array  $params 待检测参数
     *
     * @return bool
     */
    private static function checkCondition(string $params) : bool
    {
        if (false !== $pos = strpos($params, '@')) {
            return static::checkPathCondition(substr($params,0,$pos)) && static::checkQueryCondition(substr($params, $pos + 1));
        } else {
            return static::checkPathCondition($params);
        }
    }

    private static function checkPathCondition(string $path) : bool
    {
        $target_path = explode('/', $path);
        $pending_path = static::$pending_path ?: [''];

        if (count($target_path) !== $len = count($pending_path)) {
            return false;
        }

        if ($target_path[0] !== $pending_path[0]) {
            return false;
        }

        for ($i = 1; $i < $len; ++$i ) {
            if (false !== $pos = strpos($target_path[0], '(')) {
                $name = substr($target_path[0],0,$pos);
                $roule_key = substr($target_path[0],$pos +1, -1);
                if (static::checkRoule($pending_path[0], $roule_key)) {
                    $_GET[$name] = $pending_path[0];
                    continue;
                }
            } elseif(isset($target_path[1]) && $pending_path[0] === $target_path[0]) {
                if (static::checkRoule($pending_path[1], $target_path[1])) {
                    $_GET[$pending_path[0]] = $pending_path[1];
                    ++$i;
                    continue;
                }
            }
            return false;
        }

        return true;
    }
    private static function checkQueryCondition(string $query) : bool
    {
        while (isset($query[0])) {
            [$query, $args_name, $source] = static::getArgsInfoByQuery($query);

            foreach ($args_name as $arg_name) {
                if (false !== $pos = strpos($arg_name, '(')) {
                    // 检测
                    if (false === static::checkRoule($source[substr($arg_name,0,$pos)], substr($arg_name,$pos +1, -1))) {
                        return false;
                    }
                }
            }
        }
        return true;
    }
}