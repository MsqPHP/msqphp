<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;

trait RouteStaticTrait
{
    public static function static(\Closure $func, $args = []) : void
    {
        // 未匹配,直接返回
        if (static::$matched) {
            return;
        }
        // 调用闭包函数
        call_user_func_array($func,$args);
        // 未匹配成功或者不支持静态,则不属于static路由闭包函数包括范围,返回
        if (!static::$matched || !HAS_STATIC) {
            return;
        }
        base\file\File::write(static::getStaticPath(), '<?php declare(strict_types = 1);
        const APP_DEBUG  = ' . (APP_DEBUG  ? 'true' : 'false') . ';
        const HAS_CACHE  = ' . (HAS_CACHE  ? 'true' : 'false') . ';
        const HAS_VIEW   = ' . (HAS_VIEW   ? 'true' : 'false') . ';
        const HAS_STATIC = ' . (HAS_STATIC ? 'true' : 'false') . ';

        require \'' . \msqphp\Environment::getPath('bootstrap') . 'framework/base_app.php\';
        require \'' . \msqphp\Environment::getPath('bootstrap') . 'framework/loader.php\';
        require \'' . \msqphp\Environment::getPath('bootstrap') . 'framework/loader.php\';
        require \'' . \msqphp\Environment::getPath('bootstrap') . 'framework/function.php\';
        require \'' . \msqphp\Environment::getPath('bootstrap') . 'framework/user.php\';

        // 控制器加路由开始时间
        define(\'ROUTE_START\', microtime(true));
        define(\'ROUTE_CONTROLLER_START\', microtime(true));

        \msqphp\core\route\Route::parseQuery($_SERVER[\'QUERY_STRING\']);
        \msqphp\core\route\Route::initStaticEnvironment('.var_export(static::getStaticInfo(), true).');
        \msqphp\core\route\Roue:::runStaticFunc();
        // 控制器加路由结束时间
        define(\'ROUTE_END\', microtime(true));
        \msqphp\App::end();
        ');

    }

    /**
     * 获取静态路径
     *
     * @return string
     */
    public static function getStaticPath() : string
    {
        $path = trim(strtr(static::getPath(), '/', DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);

        empty($path) || $path .= DIRECTORY_SEPARATOR;

        return \msqphp\Environment::getPath('public') . $path . 'index.php';
    }

    /**
     * 设置路由信息
     *
     * @param array $info = [
     * ]
     */
    public static function initStaticEnvironment(array $info) : void
    {
        include \msqphp\Environment::getPath('application').'route_rule.php';
        static::$category_info = $info['category_info'];
        foreach ($category_info['constant'] as $key => $value) {
            defined($key) || define($key, $value);
        }
        static::$method_info = $info['method_info'];
        // 重新解析get参数
        static::getQuery();
    }
    public static function runStaticFunc() : void
    {
        static::checkMethod(static::$method_info['method']);
        static::checkCondition(static::$method_info['condition']);
        define('USER_FUNC_START', microtime(true));

        $class_name = static::$method_info['function']['class'];
        $method = static::$method_info['function']['method'];
        $query = static::$method_info['function']['query'];
        $args = static::$method_info['function']['args'];

        call_user_func_array([new $class_name, $method], static::getArgsByQuery($query, $args));
        define('USER_FUNC_END', microtime(true));
    }
    public static function getStaticInfo() : array
    {
        return [
            'method_info' => static::$method_info,
            'category_info' => static::$category_info,
        ];
    }
}