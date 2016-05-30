<?php declare(strict_types = 1);
namespace msqphp;

class App
{
    /**
     * 框架运行
     *
     * @return void
     */
    public static function run()
    {

        register_shutdown_function('\msqphp\App::end');

        //执行定时任务
        if (!defined('NO_CAHCE') && core\config\Config::get('framework.cron')) {
            $path = Environment::getPath('storage').'framework'.DIRECTORY_SEPARATOR.'cron'.DIRECTORY_SEPARATOR.'cron.log';
            if (is_file($path) && time() - core\config\Config::get('framework.cron_intervals') > filemtime($path)) {
                require __DIR__.'/core/cron/Cron.php';
                core\cron\Cron::getInstance();
            }
        }

        //控制器加路由开始时间
        define('ROUTE_CONTROLLER_START', microtime(true));

        try {

            //加载路由并运行
            require __DIR__.'/core/route/Route.php';
            core\route\Route::run();

        } catch(core\route\RouteException $e) {

            base\response\Response::error($e->getMessage());

        } catch(\msqphp\core\exception\Exception $e) {

            base\response\Response::error($e->getMessage());
        }

        //控制器加路由结束时间
        define('ROUTE_CONTROLLER_END', microtime(true));

        static::end();
    }
    /**
     * 框架结束
     *
     * @return void
     */
    public static function end()
    {
        static $ended = false;
        //避免多次调用,应该不会出现的吧.
        if ($ended) {
            return;
        } else {
            $ended = true;
        }

        //结束调用,产生框架运行信息;
        if (0 !== APP_DEBUG && 5 !== APP_DEBUG) {
            if (defined('PHP_START_CPU')) {
                $end_cpu = getrusage();
            }
            if (defined('PHP_START_MEM')) {
                $end_mem = memory_get_usage();
            }
            if (defined('PHP_INIT_TIME')) {
                $end_time = microtime(true);
            }
            $end_info = [];
            if (isset($end_time)) {
                $end_info[] = '时间信息:';
                $end_info[] = "\t现在时间戳      : " . time();
                $end_info[] = "\t现在时间        : " . date('Y-m-d H:i:s');
                $end_info[] = "\t总用时          : " . ($end_time          - PHP_INIT_TIME         ) . '秒';
                $end_info[] = "\t初始化用时      : " . (PHP_START_TIME     - PHP_INIT_TIME         ) . '秒';
                $end_info[] = "\t框架总用时      : " . ($end_time          - PHP_START_TIME        ) . '秒';
                $end_info[] = "\t路由加控制器用时: " . (ROUTE_CONTROLLER_END - ROUTE_CONTROLLER_START) . '秒';
            }
            if (class_exists('\msqphp\core\database\Database', false)) {
                $end_info[] = 'sql语句:';
                $end_info[] = core\database\Database::getSqls();
            }
            if (isset($end_mem)) {
                $end_info[] = '内存信息:';
                $end_info[] = "\t开始内存: " . base\number\Number::byte(PHP_START_MEM, false);
                $end_info[] = "\t结束内存: " . base\number\Number::byte($end_mem, false);
                $end_info[] = "\t内存差值: " . base\number\Number::byte($end_mem-PHP_START_MEM, false);
                $end_info[] = "\t内存峰值: " . base\number\Number::byte(memory_get_peak_usage(), false);
            }
            if (isset($end_cpu)) {
                $end_info[] = 'cpu信息:';
                $end_info[] = "\t块输出操作    : 开始:" . PHP_START_CPU['ru_oublock']. ", \t结束:" . $end_cpu['ru_oublock'];
                $end_info[] = "\t块输入操作    : 开始:" . PHP_START_CPU['ru_inblock']. ", \t结束:" . $end_cpu['ru_inblock'];
                $end_info[] = "\t最大驻留集大小: 开始:" . PHP_START_CPU['ru_maxrss'] . ", \t结束:" . $end_cpu['ru_maxrss'];
                $end_info[] = "\t主动上下文切换: 开始:" . PHP_START_CPU['ru_nvcsw']  . ", \t结束:" . $end_cpu['ru_nvcsw'];
                $end_info[] = "\t被动上下文切换: 开始:" . PHP_START_CPU['ru_nivcsw'] . ", \t结束:" . $end_cpu['ru_nivcsw'];
                $end_info[] = "\t页回收        : 开始:" . PHP_START_CPU['ru_minflt'] . ", \t结束:" . $end_cpu['ru_minflt'];
                $end_info[] = "\t页失效        : 开始:" . PHP_START_CPU['ru_majflt'] . ", \t结束:" . $end_cpu['ru_majflt'];
            }
            if (function_exists('get_required_files')) {
                $files = get_required_files();
                $all_size = 0;
                $file_info = [];
                foreach ($files as $file) {
                    $byte = filesize($file);
                    $file_info[] = $file.'大小:'.base\number\Number::byte($byte, false);
                    $all_size += $byte;
                }
                $end_info[] = '加载信息:';
                $end_info[] = '总共加载文件:'.count($files).'个, 大小:'.base\number\Number::byte($all_size, false);
                $end_info = array_merge($end_info, $file_info);
            }
            $end_info[] = 'composer加载文件个数:'.count(Environment::$autoload_classes);
            $end_info[] = 'composer加载文件列表:';
            $end_info[] = Environment::$autoload_classes;

            empty($end_info) || base\response\Response::dumpArray($end_info, true);
        }
    }
}