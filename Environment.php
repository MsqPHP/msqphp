<?php declare(strict_types = 1);
namespace msqphp;

class Environment
{
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
    public static function run(array $path_config)
    {
        //开始cpu状况
        function_exists('getrusage') && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && define('PHP_START_CPU', getrusage());

        //开始内存状况
        function_exists('memory_get_usage') && define('PHP_START_MEM' , memory_get_usage());

        //配置路径
        static::initPath($path_config);
        unset($path_config);

        //获得运行环境
        static::$sapi = PHP_SAPI === 'cli' ? 'cli' : 'cgi';

        //配置环境
        static::initDebug();

        //引用composer加载文件数组(需要自己修改代码实现)
        static::$autoload_classes = & $GLOBALS['autoloader_class'];

        //如果有缓存,则初始化
        defined('NO_CACHE') || static::initAutoload();

        //配置配置
        static::initConfig();
        //时区设置
        date_default_timezone_set(core\config\Config::get('framework.timezone'));

        //如果有缓存,则结束
        defined('NO_CACHE') || static::endAutoload();
        //分情况运行
        if ('cli' === static::$sapi) {
            //命令行模式
            require __DIR__.'/Cli.php';
            Cli::run();
        } elseif (4 === APP_DEBUG) {
            require __DIR__.'/Test.php';
            Test::run();
        } elseif (5 === APP_DEBUG) {
            base\response\Response::unavailable();
        } else {
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
    public static function initPath(array $path_config)
    {
        //初始化路径
        static::$path = array_map(function (string $path) {
            //是目录则realpath
            if (is_dir($path)) {
                return realpath($path) . DIRECTORY_SEPARATOR;
            } else {
            //报错
                base\response\Response::error($path.'不存在');
            }
        }, $path_config);
    }
    /**
     * 初始化自动加载
     * 实现原理:
     * $info = [
     *     'last'     => [],//最终需要加载的文件
     *     'needful'  => [],//需要加载的文件,但不确定依赖关系(加载顺序),
     *     'tidied'   => [],//整理过的文件列表,直接依次加载即可
     * ];
     * 主要逻辑为对数据的处理
     * 如果$info不存在,取空,返回
     * 如果$info['last']存在并且$info['needful']不存在,直接加载返回
     * 其他情况进行整理
     *
     * @return void
     */
    private static function initAutoload()
    {
        require __DIR__.'/traits/Instance.php';
        require __DIR__.'/core/aiload/AiLoad.php';
        core\aiload\AiLoad::getInstance()->init()->key('environment')->load();
    }
    /**
     * 加载默认配置
     *
     * @return void
     */
    private static function initConfig()
    {
        try {

            //载入文件并初始化
            require __DIR__.'/core/config/Config.php';
            core\config\Config::init();

        } catch(core\config\ConfigException $e) {

            base\response\Response::error($e->getMessage());
        }
    }
    /**
     * 初始化调试|生产模式
     * APP_DEBUG
     * 0:生产模式
     * 1:有所有缓存模式的模拟模式(无静态)
     * 2:没有视图缓存的调试模式
     * 3:没有缓存的调试模式
     * 4:test模式
     * 5:维护模式(直接访问503页面)
     *
     * @return void
     */
    private static function initDebug()
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

        //辅助常量在测试模式时使用
        if (1 === APP_DEBUG || 2 === APP_DEBUG || 3 === APP_DEBUG) {
            APP_DEBUG && define('NO_STATIC', true);
            1 < APP_DEBUG && define('NO_VIEW', true);
            2 < APP_DEBUG && define('NO_CACHE', true);
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
    private static function endAutoload()
    {
        $autoload = core\aiload\AiLoad::getInstance();
        if ($autoload->changed()) {
            static::$autoload_changed = true;
            $autoload->update()->save()->end();
        } else {
            rand(0,5000) === 1000 && $autoload->delete();
            $autoload->end();
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