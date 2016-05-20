<?php declare(strict_types = 1);
namespace msqphp;

class Environment
{
    //所有目录存放
    private static $path = [];
    /**
     * 框架配置
     * 
     * @return void
     */
    public static function init(array $path_config)
    {
        define('FRAMEWORK_INIT_START', microtime(true));
        //初始化路径
        static::$path = array_map(
            function($path) {
                if (is_dir($path)) {
                    return realpath($path) . DIRECTORY_SEPARATOR;
                } else {
                    base\response\Response::error($path.'不存在');
                }
            }, 
            $path_config
        );
        //debug配置
        try {
            require __DIR__.'/core/debug/Debug.php';
            core\debug\Debug::start();
        } catch(core\debug\DebugException $e) {
            base\response\Response::error($e->getMessage());
        }

        //初始化配置
        try {
            require __DIR__.'/core/config/Config.php';
            core\config\Config::init();
        } catch(core\config\ConfigException $e) {
            base\response\Response::error($e->getMessage());
        }
        define('FRAMEWORK_INIT_END', microtime(true));
    }
    /**
     * 框架开始
     * 
     * @return void
     */
    public static function start()
    {
        define('FRAMEWORK_START_START', microtime(true));
        try {
            //得到路由信息
            require __DIR__.'/core/route/Route.php';
            core\route\Route::parseUrl();
        } catch(core\route\RouteException $e) {
            static::$error($e->getMessage());
        }
        define('FRAMEWORK_START_END', microtime(true));
    }
    /**
     * 框架运行
     * 
     * @return void
     */
    public static function run()
    {
        define('FRAMEWORK_RUN_START', microtime(true));
        try {
            core\route\Route::run();
        } catch(core\route\RouteException $e) {
            base\response\Response::error($e->getMessage());
        }
        define('FRAMEWORK_RUN_END', microtime(true));
    }
    /**
     * 框架结束
     * 
     * @return void
     */
    public static function end()
    {
        //调试程序
        try {
            core\debug\Debug::end();
        } catch(core\Debug\DebugException $e) {
            base\response\Response::error($e->getMessage());
        }
    }
    /**
     * 获得路径
     * 
     * @param  string $name 名称
     * @return string
     */
    public static function getPath(string $name) : string
    {
        return static::$path[$name];
    }
    /**
     * 设置路径
     *
     * @param  string  $name 名称
     * @param  string  $path 路径
     * @return void
     */
    public static function setPath(string $name, string $path)
    {
        if (is_dir($path)) {
            static::$path[$name] = realpath($path).DIRECTORY_SEPARATOR;
        } else {
            base\response\Response::error($path.'不存在');
        }
    }
}