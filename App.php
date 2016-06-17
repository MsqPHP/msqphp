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
        //执行定时任务
        require __DIR__.'/core/cron/Cron.php';
        if (core\config\Config::get('framework.cron')) {

            $path = Environment::getPath('storage').'framework'.DIRECTORY_SEPARATOR.'cron'.DIRECTORY_SEPARATOR.'cron.log';

            if (!is_file($path) || filemtime($path) + core\config\Config::get('framework.cron_intervals') < time()) {
                core\cron\Cron::getInstance()->run();
            }
            unset($path);
        }

        //控制器加路由开始时间
        define('ROUTE_CONTROLLER_START', microtime(true));

        try {

            //加载路由并运行
            require __DIR__.'/core/route/RouteRouleTrait.php';
            require __DIR__.'/core/route/RouteGroupTrait.php';
            require __DIR__.'/core/route/RouteLimiteTrait.php';
            require __DIR__.'/core/route/RouteMethodTrait.php';
            require __DIR__.'/core/route/Route.php';


            core\route\Route::run();

        } catch(core\route\RouteException $e) {

            core\response\Response::error('Route执行错误,原因:'.$e->getMessage());

        } catch(core\exception\Exception $e) {

            core\response\Response::error($e->getMessage());

        } catch (\Exception $e) {

            throw $e;
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
        if (APP_DEBUG === 0) {
            core\log\Log::getInstance()->init()->message('运行成功')->content(static::getSimalInfo())->level('success')->recode();
        } elseif (APP_DEBUG === 1) {
            core\log\Log::getInstance()->init()->message('运行成功')->content(static::getSimalInfo())->level('success')->recode();
            core\response\Response::dumpArray(static::getFullInfo());
        } else {
            core\response\Response::dumpArray(static::getFullInfo());
        }
    }
    private static function getSimalInfo() : array
    {
        $end_info = [];
        //结束调用,产生框架运行信息;
        defined('PHP_INIT_TIME') && $end_time = microtime(true);

        if (isset($end_time)) {
            $end_info[] = "\t总用时          : " . (string) round($end_time      - PHP_INIT_TIME , 12) . '秒';
            defined('ROUTE_CONTROLLER_END') && $end_info[] = "\t路由加控制器用时: " . (string) round(ROUTE_CONTROLLER_END - ROUTE_CONTROLLER_START, 12) . '秒';
            $end_info[] = "\t内存峰值: " . base\number\Number::byte(memory_get_peak_usage(), false);
        }

        if (class_exists('\msqphp\core\database\Database', false) && !empty($times = core\database\Database::getTimes())) {
            $times = core\database\Database::getTimes();
            $end_info[] = "\t".'sql语句:'.count($times['sqls']).'条';
            $end_info[] = "\t".'sql总用时:'.(string) round($times['total'], 12).'秒';
        }

        return $end_info;
    }
    private static function getFullInfo() : array
    {
        defined('PHP_INIT_TIME') && $end_time = microtime(true);
        defined('PHP_START_MEM') && $end_mem = memory_get_usage();
        defined('PHP_START_CPU') && $end_cpu = getrusage();

        $end_info = [];

        if (isset($end_time)) {
            $end_info[] = '时间信息:';
            $end_info[] = "\t现在时间戳      : " . (string) round(microtime(true)                , 12) . '秒';
            $end_info[] = "\t现在时间        : " . date('Y-m-d H:i:s');
            $end_info[] = "\t总用时          : " . (string) round($end_time      - PHP_INIT_TIME , 12) . '秒';
            $end_info[] = "\t初始化用时      : " . (string) round(PHP_START_TIME - PHP_INIT_TIME , 12) . '秒(composer)';
            $end_info[] = "\t框架总用时      : " . (string) round($end_time      - PHP_START_TIME, 12) . '秒';
            defined('ROUTE_CONTROLLER_END') && $end_info[] = "\t框架实际用时    : " . (string) round($end_time      - PHP_START_TIME - ROUTE_CONTROLLER_END + ROUTE_CONTROLLER_START, 12) . '秒';
            defined('ROUTE_CONTROLLER_END') && $end_info[] = "\t路由加控制器用时: " . (string) round(ROUTE_CONTROLLER_END - ROUTE_CONTROLLER_START, 12) . '秒';
            unset($end_time);
        }
        if (class_exists('\msqphp\core\database\Database', false) && !empty($times = core\database\Database::getTimes())) {
            $end_info[] = 'sql信息:';
            $end_info[] = "\t".'sql初始化用时:'.$times['init'];
            $end_info[] = "\t".'sql语句:'.count($times['sqls']).'条';

            array_map(function ($sql_info) use (& $end_info) {
                $end_info[] = "\t".$sql_info['sql']."\t\t".'用时'.$sql_info['time'];
            }, $times['sqls']);

            $end_info[] = "\t".'sql总用时:'.$times['total'];
            unset($times);
        }

        if (isset($end_mem)) {
            $end_info[] = '内存信息:';
            $end_info[] = "\t开始内存: " . base\number\Number::byte(PHP_START_MEM, false);
            $end_info[] = "\t结束内存: " . base\number\Number::byte($end_mem, false);
            $end_info[] = "\t内存差值: " . base\number\Number::byte($end_mem-PHP_START_MEM, false);
            $end_info[] = "\t内存峰值: " . base\number\Number::byte(memory_get_peak_usage(), false);
            unset($end_mem);
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
            $end_info[] = "\t用户用时      : " . ($end_cpu['ru_utime.tv_sec'] - PHP_START_CPU['ru_utime.tv_sec'] + ($end_cpu['ru_utime.tv_usec'] - PHP_START_CPU['ru_utime.tv_usec']) / 1000000) . '秒';
            $end_info[] = "\t系统用时      : " . ($end_cpu['ru_stime.tv_sec'] - PHP_START_CPU['ru_stime.tv_sec'] + ($end_cpu['ru_stime.tv_usec'] - PHP_START_CPU['ru_stime.tv_usec']) / 1000000) . '秒';
            unset($end_cpu);
        }
        if (function_exists('get_defined_constants')) {
            $end_info[] = '常量信息:';
            foreach (get_defined_constants(true)['user'] as $key => $value) {
                is_array($value) && $value = var_export($value, true);
                is_bool($value) && $value = $value ? 'true' : 'false';
                $end_info[] = "\t".'常量:'.$key."\t=>\t".$value;
            }
        }
        if (function_exists('get_required_files')) {
            $files      = get_required_files();
            $all_size   = 0;
            $end_info[] = '加载信息:';
            $file_info  = [];
            foreach ($files as $file) {
                $byte        = filesize($file);
                $file_info[] = "\t".'文件:'.$file."\t\t".'大小:'.base\number\Number::byte($byte, false);
                $all_size    += $byte;
            }
            $end_info[] = "\t".'总共加载文件:'.count($files).'个, 大小:'.base\number\Number::byte($all_size, false);
            $end_info   = array_merge($end_info, $file_info);
            unset($file_info);
            unset($files);
            unset($all_size);
            unset($file);
            unset($byte);
        }
        $composer = array_merge(Environment::$autoload_classes, core\aiload\AiLoad::$composer);
        if (!empty($composer)) {
            $end_info[] = 'composer加载文件个数(不准确,可能少一到两个):'.count($composer);
            $end_info[] = 'composer加载文件列表:';
            foreach ($composer as $file) {
                $end_info[] = "\t".'文件:'.$file."\t\t".'大小:'.base\number\Number::byte(filesize($file), false);
            }
        }

        return $end_info;
    }
}