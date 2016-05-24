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
            $blank = "\t";
            if (isset($end_time)) {
                show('时间信息:');
                show($blank . '总共时间  :'     . $blank . ($end_time - PHP_INIT_TIME )   . '秒');
                show($blank . '初始化时间:'     . $blank . (PHP_START_TIME-PHP_INIT_TIME) . '秒');
                show($blank . '控制器用时:'     . $blank . (PHP_CONT_END-PHP_CONT_START)  . '秒');
                show($blank . '框架总用时  :'   . $blank . ($end_time-PHP_START_TIME-(PHP_CONT_END-PHP_CONT_START)) . '秒');
                show($blank . '环境搭建用时:'   . $blank . (FRAMEWORK_INIT_END-FRAMEWORK_INIT_START)   .'秒');
                show($blank . '环境开始用时:'   . $blank . (FRAMEWORK_START_END-FRAMEWORK_START_START) .'秒');
                show($blank . '环境运行用时:'   . $blank . (FRAMEWORK_RUN_END-FRAMEWORK_RUN_START-(PHP_CONT_END-PHP_CONT_START))     .'秒');
            }
            if (isset($end_mem)) {
                show('内存信息:');
                show($blank . '开始内存:'  . $blank . base\number\Number::byte(PHP_START_MEM, false));
                show($blank . '结束内存:'  . $blank . base\number\Number::byte($end_mem, false));
                show($blank . '内存差值:'  . $blank . base\number\Number::byte($end_mem-PHP_START_MEM, false));
                show($blank . '内存峰值:'  . $blank . base\number\Number::byte(memory_get_peak_usage(), false));
            }
            if (isset($end_cpu)) {
                show('cpu信息:');
                $cpu_utime = ($end_cpu['ru_utime.tv_sec'] - PHP_START_CPU['ru_utime.tv_sec']) + (($end_cpu['ru_utime.tv_usec'] - PHP_START_CPU['ru_utime.tv_usec'])/ 1000000);
                $cpu_stime = ($end_cpu['ru_stime.tv_sec'] - PHP_START_CPU['ru_stime.tv_sec']) + (($end_cpu['ru_stime.tv_usec'] - PHP_START_CPU['ru_stime.tv_usec'])/ 1000000);
                show($blank . '块输出操作    :' . $blank . '开始:'.PHP_START_CPU['ru_oublock']. ', 结束:' . $end_cpu['ru_oublock']);
                show($blank . '块输入操作    :' . $blank . '开始:'.PHP_START_CPU['ru_inblock']. ', 结束:' . $end_cpu['ru_inblock']);
                show($blank . '最大驻留集大小:' . $blank . '开始:'.PHP_START_CPU['ru_maxrss'] . ', 结束:' . $end_cpu['ru_maxrss']);
                show($blank . '主动上下文切换:' . $blank . '开始:'.PHP_START_CPU['ru_nvcsw']  . ', 结束:' . $end_cpu['ru_nvcsw']);
                show($blank . '被动上下文切换:' . $blank . '开始:'.PHP_START_CPU['ru_nivcsw'] . ', 结束:' . $end_cpu['ru_nivcsw']);
                show($blank . '页回收        :' . $blank . '开始:'.PHP_START_CPU['ru_minflt'] . ', 结束:' . $end_cpu['ru_minflt']);
                show($blank . '页失效        :' . $blank . '开始:'.PHP_START_CPU['ru_majflt'] . ', 结束:' . $end_cpu['ru_majflt']);
                show($blank . '用户操作时间  :' . $blank . $cpu_utime.'秒');
                show($blank . '系统操作时间  :' . $blank . $cpu_stime.'秒');
            }
            if (function_exists('get_required_files')) {
                show('加载信息:');
                $files = get_required_files();
                $all_size = 0;
                foreach ($files as $file) {
                    $all_size += filesize($file);
                }
                show('总共加载文件:'.count($files).'个, 大小:'.base\number\Number::byte($all_size, false));
                show($files);
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
}