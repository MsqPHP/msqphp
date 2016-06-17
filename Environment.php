<?php declare(strict_types = 1);
namespace msqphp;

class Environment
{
    //框架版本
    const vension = 1.2;

    //所有目录存放
    private static $path = [];

    //当前运行环境
    private static $sapi = '';

    //环境的自动加载是否有变化,会影响到route的自动加载
    public static $autoload_changed    = false;

    //composer自动加载文件列表
    public static $autoload_classes    = [];

    /**
     * 运行
     *
     * @param  array  $path_config 路径配置
     *
     * @return void
     */
    public static function run(array $path_config) : void
    {
        //开始cpu状况
        function_exists('getrusage') && 0 !== strncasecmp(PHP_OS, 'WIN', 3) && define('PHP_START_CPU', getrusage());

        //开始内存状况
        define('PHP_START_MEM' , memory_get_usage());

        //配置路径
        static::setPath($path_config);
        unset($path_config);

        //获得运行环境
        static::$sapi = PHP_SAPI === 'cli' ? 'cli' : 'cgi';

        if (!defined('COMPOSER_AUTOLOAD') || !COMPOSER_AUTOLOAD) {
            require __DIR__.'/core/autoload/Autoload.php';
            core\autoload\Autoload::register();
        }

        //配置环境
        static::initDebug();

        //如果有缓存,则初始化
        // static::initAiload();

        //配置配置
        static::initConfig();

        //时区设置
        date_default_timezone_set(core\config\Config::get('framework.timezone'));

        //如果有缓存,则结束
        // static::endAiload();

        //命令行模式
        if ('cli' === static::$sapi) {
            require __DIR__.'/Cli.php';
            Cli::run();

        //测试模式
        } elseif (4 === APP_DEBUG) {
            require __DIR__.'/Test.php';
            Test::run();

        //维护模式
        } elseif (5 === APP_DEBUG) {
            core\response\Response::unavailable();
        } else {

        //应用模式
            require __DIR__.'/App.php';
            App::run();
        }
    }
    /**
     * 初始化路径
     *
     * @param  array $path_config 路径配置
     *
     * @return void
     */
    public static function setPath(array $path_config) : void
    {
        //初始化路径
        static::$path = array_map(function (string $path) {

            //存在或报错
            is_dir($path) || core\response\Response::error('Environment初始化错误,原因:'.$path.'不存在');

            //是目录则realpath
            return realpath($path) . DIRECTORY_SEPARATOR;

        }, $path_config);
    }
    /**
     * 初始化智能加载
     *
     * @return void
     */
    private static function initAiload() : void
    {
        //引用composer加载文件数组(需要自己修改代码实现)
        static::$autoload_classes = & $GLOBALS['autoloader_class'];

        //载入aiload类依赖的trait Instance
        require __DIR__.'/traits/Instance.php';
        //载入aiload类文件
        require __DIR__.'/core/aiload/AiLoad.php';

        //载入
        core\aiload\AiLoad::getInstance()->init()->key('environment')->load();
    }
    /**
     * 加载默认配置
     *
     * @return void
     */
    private static function initConfig() : void
    {
        try {

            //载入文件并初始化
            require __DIR__.'/core/config/Config.php';
            core\config\Config::init();

        } catch(core\config\ConfigException $e) {

            core\response\Response::error($e->getMessage());
        }
    }
    /**
     * 初始化调试|生产模式
     * APP_DEBUG
     * 0:生产模式
     * 1:无静态开发模式,几乎等同于生产模式
     * 2:无视图缓存开发模式
     * 3:无缓存开发模式
     * 4:test模式
     * 5:维护模式(直接访问503页面)
     *
     * @return void
     */
    private static function initDebug() : void
    {
        //错误处理方式
        if (0 === APP_DEBUG || 5 === APP_DEBUG) {

            //设置错误级别最低
            error_reporting(0);
            //错误不显示
            ini_set('display_errors', 'Off');
            //开启日志记录
            ini_set('log_errors', 'On');
            //日志文件
            ini_set('error_log', static::getPath('storage').'error.log');
        } else {

            //设置错误级别最高
            error_reporting(E_ALL);
            //错误显示
            ini_set('display_errors', 'On');
            //取消日志记录
            ini_set('log_errors', 'Off');
        }

        //载入错误类,设置错误函数处理方式
        require __DIR__.'/core/error/Error.php';
        core\error\Error::register();

        //辅助常量在开发模式时使用
        if (1 === APP_DEBUG || 2 === APP_DEBUG || 3 === APP_DEBUG) {
            APP_DEBUG     && define('NO_STATIC', true);
            1 < APP_DEBUG && define('NO_VIEW',   true);
            2 < APP_DEBUG && define('NO_CACHE',  true);
        }

    }
    /**
     * 自动加载结束
     *
     * 主要负责处理需要加载那些文件
     * 同时如果加载了新文件,重新生成
     *
     * @return void
     */
    private static function endAiload() : void
    {
        //获得实例
        $autoload = core\aiload\AiLoad::getInstance();

        //改变过
        if (static::$autoload_changed = $autoload->last()) {

            //随机删除
            rand(0,5000) === 1000 && $autoload->deleteAll();

            //结束
            $autoload->end();

        } else {

            //更新保存并结束
            $autoload->update()->end();
        }
    }
    /**
     * 获得路径
     *
     * @param  string $name 名称
     *
     * @return string
     */
    public static function getPath(string $name) : string
    {
        return static::$path[$name];
    }
    /**
     * 获得当前运行环境
     *
     * @return string
     */
    public static function getSapi() : string
    {
        return static::$sapi;
    }
}