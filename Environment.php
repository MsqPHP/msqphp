<?php declare(strict_types = 1);
namespace msqphp;

class Environment
{
    //所有目录存放
    private static $path = [];

    private static $sapi = '';
    /**
     * 框架配置
     *
     * @return void
     */
    public static function init(array $path_config)
    {
        define('FRAMEWORK_INIT_START', microtime(true));
        static::$sapi = PHP_SAPI === 'cli' ? 'cli' : (false !== strpos(PHP_SAPI, 'apache') ? 'apche' : '');
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
        unset($path_config);
        //debug配置
        try {
            require __DIR__.'/core/debug/Debug.php';
            core\debug\Debug::init();
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

        if ('cli' === static::$sapi) {
            try {
                //得到路由信息
                require __DIR__.'/core/cli/Cli.php';
                core\cli\Cli::init();
            } catch(core\cli\CliException $e) {
                static::$error($e->getMessage());
            }
        } else {
            try {
                //得到路由信息
                require __DIR__.'/core/route/Route.php';
                core\route\Route::init();
            } catch(core\route\RouteException $e) {
                static::$error($e->getMessage());
            }
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
        if ('cli' === static::$sapi) {
            try {
                core\cli\Cli::run();
            } catch(core\cli\CliException $e) {
                static::$error($e->getMessage());
            }
        } else {
            try {
                core\route\Route::run();
            } catch(core\route\RouteException $e) {
                base\response\Response::error($e->getMessage());
            }
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
        if (APP_DEBUG > 0) {
            if (defined('PHP_START_CPU')) {
                $end_cpu = getrusage();
            }
            if (defined('PHP_START_MEM')) {
                $end_mem = memory_get_usage();
            }
            if (defined('PHP_INIT_TIME')) {
                $end_time = microtime(true);
            }
            if (isset($end_time)) {
                base\response\Response::dump('时间信息:');
                base\response\Response::dump("\t总共时间    :" . ($end_time          - PHP_INIT_TIME         ) . '秒');
                base\response\Response::dump("\t初始化时间  :" . (PHP_START_TIME     - PHP_INIT_TIME         ) . '秒');
                base\response\Response::dump("\t框架总用时  :" . ($end_time          - PHP_START_TIME        ) . '秒');
                base\response\Response::dump("\t环境搭建用时:" . (FRAMEWORK_INIT_END - FRAMEWORK_INIT_START  ) . '秒');
                base\response\Response::dump("\t环境开始用时:" . (FRAMEWORK_START_END- FRAMEWORK_START_START ) . '秒');
                base\response\Response::dump("\t环境运行用时:" . (FRAMEWORK_RUN_END  - FRAMEWORK_RUN_START   ) . '秒');
            }
            if (isset($end_mem)) {
                base\response\Response::dump('内存信息:');
                base\response\Response::dump("\t开始内存:" . base\number\Number::byte(PHP_START_MEM, false));
                base\response\Response::dump("\t结束内存:" . base\number\Number::byte($end_mem, false));
                base\response\Response::dump("\t内存差值:" . base\number\Number::byte($end_mem-PHP_START_MEM, false));
                base\response\Response::dump("\t内存峰值:" . base\number\Number::byte(memory_get_peak_usage(), false));
            }
            if (isset($end_cpu)) {
                base\response\Response::dump('cpu信息:');
                base\response\Response::dump("\t块输出操作    :开始:".PHP_START_CPU['ru_oublock']. ", \t结束:" . $end_cpu['ru_oublock']);
                base\response\Response::dump("\t块输入操作    :开始:".PHP_START_CPU['ru_inblock']. ", \t结束:" . $end_cpu['ru_inblock']);
                base\response\Response::dump("\t最大驻留集大小:开始:".PHP_START_CPU['ru_maxrss'] . ", \t结束:" . $end_cpu['ru_maxrss']);
                base\response\Response::dump("\t主动上下文切换:开始:".PHP_START_CPU['ru_nvcsw']  . ", \t结束:" . $end_cpu['ru_nvcsw']);
                base\response\Response::dump("\t被动上下文切换:开始:".PHP_START_CPU['ru_nivcsw'] . ", \t结束:" . $end_cpu['ru_nivcsw']);
                base\response\Response::dump("\t页回收        :开始:".PHP_START_CPU['ru_minflt'] . ", \t结束:" . $end_cpu['ru_minflt']);
                base\response\Response::dump("\t页失效        :开始:".PHP_START_CPU['ru_majflt'] . ", \t结束:" . $end_cpu['ru_majflt']);
                $cpu_utime = ($end_cpu['ru_utime.tv_sec'] - PHP_START_CPU['ru_utime.tv_sec']) + (($end_cpu['ru_utime.tv_usec'] - PHP_START_CPU['ru_utime.tv_usec'])/ 1000000);
                base\response\Response::dump("\t用户操作时间  :" . $cpu_utime.'秒');
                $cpu_stime = ($end_cpu['ru_stime.tv_sec'] - PHP_START_CPU['ru_stime.tv_sec']) + (($end_cpu['ru_stime.tv_usec'] - PHP_START_CPU['ru_stime.tv_usec'])/ 1000000);
                base\response\Response::dump("\t系统操作时间  :" . $cpu_stime.'秒');
            }
            if (function_exists('get_required_files')) {
                base\response\Response::dump('加载信息:');
                $files = get_required_files();
                $all_size = 0;
                foreach ($files as $file) {
                    $all_size += filesize($file);
                }
                base\response\Response::dump('总共加载文件:'.count($files).'个, 大小:'.base\number\Number::byte($all_size, false));
                base\response\Response::dump($files);
            }
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

    public static function getSapi()
    {
        return static::$sapi;
    }
}