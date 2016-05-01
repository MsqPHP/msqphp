<?php declare(strict_types = 1);
namespace Msqphp;

class Environment
{
    //根目录
    static public $root_path      = '';
    //公共资源目录
    static public $public_path    = '';
    //配置目录
    static public $config_path    = '';
    //应用目录
    static public $app_path       = '';
    //资源目录
    static public $resources_path = '';
    //框架目录
    static public $framework_path = '';
    //lib目录
    static public $library_path   = '';
    /**
     * 框架配置
     * @return void
     */
    public static function init(array $path_config)
    {
        //配置路径
        static::initPath($path_config);
        //安全检测
        require __DIR__.'/Core/Safe/Safe.php';
        try {
            Core\Safe\Safe::check();
        } catch(Core\Safe\SafeException $e) {
            static::error($e->getMessage());
        }
    }
    /**
     * 框架开始
     * @return void
     */
    public static function start()
    {
        //加载缓存类
        require __DIR__.'/Core/Cache/Cache.php';
        //加载路由解析类
        require __DIR__.'/Core/Route/Route.php';
        //路由配置
        $route_config = require static::$config_path.'route.php';
        //得到路由信息
        $route_info = Core\Route\Route::getRouteInfo($route_config);
        //赋值给$_GET
        $_GET = $route_info['get'];
        //定义当前url常量
        define('URL',$route_info['url']);
        //加载app类
        require __DIR__.'/Core/App/App.php';
        //app配置
        $app_config = require static::$config_path.'app.php';
        //初始化APP
        Core\App\App::init($route_info['app'],$app_config);
    }
    /**
     * 框架运行
     * @return void
     */
    public static function run()
    {
        //app开始
        Core\App\App::start();
        //app运行
        Core\App\App::run();
        //app结束
        Core\App\App::end();
    }
    /**
     * 框架结束
     * @return void
     */
    public static function end()
    {
        if(APP_DEBUG !== 0) {
            if (defined('PHP_START_CPU')) {
                $end_cpu = getrusage();
            }
            if (defined('PHP_START_MEM')) {
                $end_mem = memory_get_usage();
            }
            $end_time = microtime(true);
            $blank = '	';
            show('时间信息:');
            show($blank . '总共时间  :'  . $blank . ($end_time - PHP_START_TIME)   . '秒');
            show($blank . '初始化时间:'  . $blank . (PHP_INIT_TIME-PHP_START_TIME) . '秒');
            show($blank . '控制器用时:'  . $blank . (PHP_CONT_END-PHP_CONT_START)  . '秒');
            show($blank . '框架用时  :'  . $blank . ($end_time-PHP_INIT_TIME-(PHP_CONT_END-PHP_CONT_START)) . '秒');
            if (isset($end_mem)) {
                show('内存信息:');
                show($blank . '开始内存:'  . $blank . Base\Str\Str::getSize(PHP_START_MEM,false));
                show($blank . '结束内存:'  . $blank . Base\Str\Str::getSize($end_mem,false));
                show($blank . '内存差值:'  . $blank . Base\Str\Str::getSize($end_mem-PHP_START_MEM,false));
                show($blank . '内存峰值:'  . $blank . Base\Str\Str::getSize(memory_get_peak_usage(),false));
            }
            if (isset($end_cpu)) {
                show('cpu信息:');
                $cpu_utime = ($end_cpu['ru_utime.tv_sec'] - PHP_START_CPU['ru_utime.tv_sec']) + (($end_cpu['ru_utime.tv_usec'] - PHP_START_CPU['ru_utime.tv_usec'])/ 1000000);
                $cpu_stime = ($end_cpu['ru_stime.tv_sec'] - PHP_START_CPU['ru_stime.tv_sec']) + (($end_cpu['ru_stime.tv_usec'] - PHP_START_CPU['ru_stime.tv_usec'])/ 1000000);
                show($blank . '块输出操作    :' . $blank . '开始:'.PHP_START_CPU['ru_oublock']. ',结束:' . $end_cpu['ru_oublock']);
                show($blank . '块输入操作    :' . $blank . '开始:'.PHP_START_CPU['ru_inblock']. ',结束:' . $end_cpu['ru_inblock']);
                show($blank . '最大驻留集大小:' . $blank . '开始:'.PHP_START_CPU['ru_maxrss'] . ',结束:' . $end_cpu['ru_maxrss']);
                show($blank . '主动上下文切换:' . $blank . '开始:'.PHP_START_CPU['ru_nvcsw']  . ',结束:' . $end_cpu['ru_nvcsw']);
                show($blank . '被动上下文切换:' . $blank . '开始:'.PHP_START_CPU['ru_nivcsw'] . ',结束:' . $end_cpu['ru_nivcsw']);
                show($blank . '页回收        :' . $blank . '开始:'.PHP_START_CPU['ru_minflt'] . ',结束:' . $end_cpu['ru_minflt']);
                show($blank . '页失效        :' . $blank . '开始:'.PHP_START_CPU['ru_majflt'] . ',结束:' . $end_cpu['ru_majflt']);
                show($blank . '用户操作时间  :' . $blank . $cpu_utime.'秒');
                show($blank . '系统操作时间  :' . $blank . $cpu_stime.'秒');
            }
            if (true) {
                show('加载信息:');
                $files = get_included_files();
                $all_size = 0;
                foreach ($files as $file) {
                    $all_size += filesize($file);
                }
                show('总共加载文件:'.count($files).'个,大小:'.Base\Str\Str::getSize($all_size,false));
                show($files);
            }
        }
    }
    private static function initPath(array $path_config)
    {
        static::$root_path      = realpath($path_config['root'])        . DIRECTORY_SEPARATOR;
        static::$framework_path = __DIR__                               . DIRECTORY_SEPARATOR;
        static::$library_path   = realpath($path_config['library'])     . DIRECTORY_SEPARATOR;
        static::$app_path       = realpath($path_config['application']) . DIRECTORY_SEPARATOR;
        static::$config_path    = realpath($path_config['config'])      . DIRECTORY_SEPARATOR;
        static::$public_path    = realpath($path_config['public'])      . DIRECTORY_SEPARATOR;
        static::$resources_path = realpath($path_config['resources'])   . DIRECTORY_SEPARATOR;
    }
    private static function error($msg)
    {
        include static::$resources_path.'views'.DIRECTORY_SEPARATOR.'500.html';
    }
}