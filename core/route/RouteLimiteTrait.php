<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;

trait RouteLimiteTrait
{
    /**
     *
     * 如未特殊声明则取一下值
     *
     * @param   Closure    $func   调用函数
     *
     * @param   array      $args   函数参数
     *
     * @param   array      $info   添加语言或主题
     *      @example $info = [
     *              'alowed'    => string(路由规则)  |  array(允许值),
     *              'default'   => string(默认值),
     *          ];
     *
     * @throws  RouteException
     *
     * @return  void
     */


    /**
     * 添加一条规则
     * @param  string $key   规则键
     * @param  string $func  正则
     * @param  Closure $func 规则回调函数
     *     @example
     *         Route::addRoule(string ':all', function() : bool {return true;});
     */
    public static function addRoule(string $key, $func)
    {
        static::$roule[$key] = $func;
    }
    //多语支持
    public static function addLanguage(array $info)
    {
        if (static::$matched) {
            return;
        }
        static::$info['language'] = $language = static::getAllowedValue($info);
        defined('__LANGUAGE__') || define('__LANGUAGE__', $language);
    }
    //多主题支持
    public static function addTheme(array $info)
    {
        if (static::$matched) {
            return;
        }
        static::$info['theme'] = $theme = static::getAllowedValue($info);
        defined('__THEME__') || define('__THEME__', $theme);
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
     */
    public static function addGroup(array $group)
    {
        if (static::$matched) {
            return;
        }
        //赋值给当前信息和分组, 键为组名, 值: 如果在允许范围内, 取其值, 否则取默认;
        static::$group[] = static::$group[$group['name']] = $group_value = static::getAllowedValue($group);

        $constat = '__'.strtoupper($group['name']).'__';
        defined($constat) || define($constat, $group_value);

        //如果命名空间存在, 取其值
        if (!isset($group['namespace'])) {
            return;
        } else {
            //bool等于组值
            if (true === $group['namespace']) {
                $namespace = $group_value;
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
    /**
     * 分组
     *
     * @param  string $group 组名
     * @param  string $value 组值
     */
    public static function group(string $group, string $value, \Closure $func, array $args = [])
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
     */
    public static function domain($domain, \Closure $func, array $args = [])
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
     */
    public static function ip($ip, \Closure $func, array $args = [])
    {
        if (static::$matched) {
            return;
        }
        static::$info['ip'] = static::$info['ip'] ?? base\ip\Ip::get();
        if (in_array(static::$info['ip'], (array)$ip)) {
            call_user_func_array($func, $args);
        }
    }
    //SSL协议, 即https限制
    public static function ssl(\Closure $func, array $args = [])
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
     * 端口限制
     *
     * @param  miexd  $port  端口,支持多
     */
    public static function port($port, \Closure $func, array $args = [])
    {
        if (static::$matched) {
            return;
        }
        static::$info['port'] = static::$info['port'] ?? $_SERVER['SERVER_PORT'];

        if (in_array(static::$info['port'], (array)$port)) {
            call_user_func_array($func, $args);
        }
    }
    /**
     * 来自url限制
     *
     * @param  miexd  $referer 来自url,支持多
     */
    public static function referer($referer, \Closure $func, array $args = [])
    {
        if (static::$matched) {
            return;
        }

        static::$info['referer'] = static::$info['referer'] ?? $_SERVER['HTTP_REFERER'];

        if (in_array(static::$info['referer'], (array)$referer)) {
            call_user_func_array($func, $args);
        }
    }

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
            $result = static::$params_handle[0];
            //取值并删除
            array_shift(static::$params_handle);
            return $result;
        } else {
            //取默认
            return $info['default'];
        }
    }
}