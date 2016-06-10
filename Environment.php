<?php declare(strict_types = 1);
namespace msqphp;

class Environment
{
    const vension = 1.1;
    //所有目录存放
    private static $path = [];
    //当前运行环境
    private static $sapi = '';
    //环境类自动加载文件信息
    private static $autoload_info      = [];
    //自动加载文件位置
    private static $autoload_file      = '';
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
        static::$path = array_map(function($path) {
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
        //缓存文件
        $file = static::$autoload_file = static::getPath('storage').'framework'.DIRECTORY_SEPARATOR.'autoload.php';

        //文件不存在,自动加载信息为空,直接返回
        if (!is_file($file)) {
            return;
        }

        //载入文件
        $info = require $file;

        //如果得到最终结果,直接加载所有文件并返回
        if (isset($info['last']) && !isset($info['needful'])) {
            array_map(function ($file) {
                require $file;
            }, $info['last']);
            return;
        }


        //加载信息改变过
        static::$autoload_changed = true;
        //需要加载的文件
        $needful = [];
        //整理过后的列表
        $tidied  = [];
        //载入整理过文件的函数
        $require_tidied_file = function (string $file) use (& $tidied) {
            require $file;
            $tidied[] = $file;
        };

        //如果有最终缓存的话,重新加载放入整理层中
        isset($info['last']) && array_map($require_tidied_file, $info['last']);

        //颠倒needful,并加载
        array_map(function($file) use(& $tidied, & $needful) {
            //如果已经加载过,再次放入needful
            if (in_array($file, static::$autoload_classes)) {
                $needful[] = $file;
            } else {
            //添加到整理过的
                $tidied[] = $file;
                require $file;
            }
        }, array_reverse($info['needful']));

        isset($info['tidied']) && array_map($require_tidied_file, $info['tidied']);

        //如果needful为空,则表示得到最终加载顺序
        static::$autoload_info = empty($needful) ? ['last'=>$tidied] : ['needful'=>$needful,'tidied'=>$tidied];
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
        //取当前信息,并将将当前的自动加载信息至空
        list($info, static::$autoload_info) = [static::$autoload_info, []];

        //如果加载文件不为空,即加载了新的文件
        if (!empty(static::$autoload_classes)) {

            //添加至必须
            $info['needful'] = array_merge($info['needful'] ?? [], static::$autoload_classes);

            //清空,避免对其他地方自动加载造成污染;
            static::$autoload_classes = [];

            //改变过,避免对其他地方自动加载造成污染;
            static::$autoload_changed = true;
        }


        //若果改变过
        if (static::$autoload_changed) {
            //取唯一值,避免重复值
            isset($info['needful']) && $info['needful'] = array_unique($info['needful']);
            //取唯一值,避免重复值
            isset($info['tidied']) && $info['tidied'] = array_unique($info['tidied']);
            //重新生存自动加载信息
            base\file\File::write(static::$autoload_file, '<?php'.PHP_EOL.'return '.var_export($info, true).';', true);
        //随机删除,重新生成
        } elseif (rand(0,5000) === 1000) {

            base\file\File::delete(static::$autoload_file, true);
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
     * 设置路径
     *
     * @param  string  $name 名称
     * @param  string  $path 路径
     *
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