<?php declare(strict_types = 1);
namespace msqphp\core\route;

use msqphp\base;

trait RouteStaticTrait
{
    public static function static(\Closure $func, $args = []) : void
    {
        if (static::$matched) {
            return;
        }

        call_user_func_array($func,$args);

        if (!static::$matched) {
            return;
        }
        if (!HAS_STATIC) {
            return;
        }
        base\file\File::write(static::getStaticPath(), '<?php declare(strict_types = 1);
        const APP_DEBUG  = ' . (APP_DEBUG  ? 'true' : 'false') . ';
        const HAS_CACHE  = ' . (HAS_CACHE  ? 'true' : 'false') . ';
        const HAS_VIEW   = ' . (HAS_VIEW   ? 'true' : 'false') . ';
        const HAS_STATIC = ' . (HAS_STATIC ? 'true' : 'false') . ';
        include \'' . \msqphp\Environment::getPath('bootstrap') . 'environment.php\';

        // 控制器加路由开始时间
        define(\'ROUTE_CONTROLLER_START\', microtime(true));

        \msqphp\core\route\Route::parseQuery($_SERVER[\'QUERY_STRING\']);

        \msqphp\core\route\Route::setParseInfo('.var_export(static::getInfo(), true).');

        $args = \msqphp\core\route\Route::getArgsByQuery(\''.static::$info['function']['query'].'\', '.static::$info['function']['args'].');

        call_user_func_array([new ' . static::$info['function']['class'] .'(), \'' . static::$info['function']['method'] . '\'], $args);

        // 控制器加路由结束时间
        define(\'ROUTE_CONTROLLER_END\', microtime(true));

        \msqphp\App::end();
        ', true);

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
     * 
     * ]
     */
    public static function setInfo(array $info) : void
    {
        if (isset($info['constant'])) {
            foreach($info['constant'] as $name => $value) {
                defined($name) || define($name, $value);
            }
        }
    }
}