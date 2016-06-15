<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;

trait RouteGroupTrait
{
    //保存分组信息的数组
    public static  $group         = [];

    /**
     * 得到允许值,用于addLanguage,addTheme,addGroup
     * 如果非默认取值则添加值当前url参数
     * 定义对应常量
     *
     * @param  array  $info 信息['alowed'=>string(路由规则)|array(指定值),'default',默认值];
     *
     * @return string
     */
    private static function getAndAddAllowedValue(array $info) : string
    {

        //如果当前url参数仍有值 并且 检测成功
        if (isset(static::$params_handle[0]) && static::checkAllowed(static::$params_handle[0], $info['allowed'])) {
            //取值
            $result = array_shift(static::$params_handle);

            //追加至Url
            static::$url .= $result . '/';
        } else {
            //取默认
            $result = $info['default'];
        }

        //定义常量
        $constat = '__'.strtoupper($info['name']).'__';
        defined($constat) || define($constat, $result);

        return $result;
    }

    /**
     * 允许值检测
     *
     * @param  string $may   可能值
     * @param  array  $value 指定值
     *
     * @return bool
     */
    private static function checkAllowed(string $may, $value) : bool
    {
        //如果是个字符串,则检测规则
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
     * 多语支持 || 多主题支持
     * @param   array      $info   添加语言或主题
     *      @example $info = [
     *              'alowed'    => string(路由规则)  |  array(允许值),
     *              'default'   => string(默认值),
     *          ];
     */
    public static function addLanguage(array $info)
    {
        $info['name']             = 'language';
        static::$info['language'] = static::getAndAddAllowedValue($info);
    }
    public static function addTheme(array $info)
    {
        $info['name']          = 'theme';
        static::$info['theme'] = static::getAndAddAllowedValue($info);
    }

    /**
     * 增加一个url分组信息
     * 将获取url参数数组中第一个参数
     * 如果在且允许,则取值并从移除
     * 否则取默认值
     *
     * @param  array $info 分组信息
     * 关联数组;
     * $info = [
     *    'name'      =>string(组名)'module',
     *    'allowed'   =>string(路由规则) || array(允许值数组)
     *    'default'   =>string('组默认值')
     *    ['namespace' =>true(与组值相同) || string('固定值') ]
     *    (可选,将在当前namespace基础上添加一个命名空间,基础值为app);
     * ];
     *
     * @return void
     */
    public static function addGroup(array $info)
    {
        //赋值给当前信息和分组, 键为组名, 值: 如果在允许范围内, 取其值, 否则取默认;
        static::$group[] = static::$group[$info['name']] = $group = static::getAndAddAllowedValue($info);

        //如果命名空间存在, 取其值
        if (isset($info['namespace'])) {
            //bool等于组值
            if (true === $info['namespace']) {
                $namespace = $group;
            } elseif (is_string($info['namespace'])) {
            //否则为过固定值
                $namespace = $info['namespace'];
            } else {
                throw new RouteException('未知的命名空间类型');
            }
            //添加到当前命名空间
            static::$namespace .= trim($namespace, '\\').'\\';
        }
    }


    /**
     * 匹配一个分组,匹配成功则调用对应函数
     *
     * @param  string   $group 组名
     * @param  string   $value 组值
     * @param  \Closure $func  闭包函数
     * @param  array    $args  参数
     *
     * @return void
     */
    public static function group(string $group, string $value, \Closure $func, array $args = [])
    {
        static::$group[$group] === $value && call_user_func_array($func, $args);
    }

    //匹配语言
    public static function language(string $language, \Closure $func, array $args = [])
    {
        static::$info['language'] === $language && call_user_func_array($func, $args);
    }

    //匹配主题
    public static function theme(string $theme, \Closure $func, array $args = [])
    {
        static::$info['theme'] === $theme && call_user_func_array($func, $args);
    }
}