<?php declare(strict_types = 1);
namespace msqphp;

final class App
{
    /**
     * 框架运行
     *
     * @return void
     */
    public static function run() : void
    {
        static::runRoute();
        // 记录或者打印信息
        if (APP_DEBUG) {
            core\response\Response::dumpArray(static::getFullInfo());
        } else {
            $content = static::getSimalInfo();
            app()->log->message('运行成功' . PHP_EOL . (empty($content) ? '' : '{' . PHP_EOL. implode(PHP_EOL, $content) . PHP_EOL . '}'))
                      ->level('success')
                      ->recode();
        }
    }

    public static function runRoute() : void
    {
        define('ROUTE_CONTROLLER_START', microtime(true));
        //加载路由并运行
        require __DIR__.'/core/route/RouteRouleTrait.php';
        require __DIR__.'/core/route/RouteCategoryTrait.php';
        require __DIR__.'/core/route/RouteLimiteTrait.php';
        require __DIR__.'/core/route/RouteMethodTrait.php';
        require __DIR__.'/core/route/RouteParseTrait.php';
        require __DIR__.'/core/route/RouteStaticTrait.php';
        require __DIR__.'/core/route/Route.php';
        core\route\Route::run();
        define('ROUTE_CONTROLLER_END', microtime(true));
    }
    private static function getSimalInfo() : array
    {
        $end_info = [];

        //结束调用,产生框架运行信息;
        defined('PHP_START_TIME') && $end_time = microtime(true);

        if (isset($end_time)) {
            $end_info[] = "\t总用时          : " . (string) round($end_time      - PHP_START_TIME , 12) . '秒';
            defined('ROUTE_CONTROLLER_END') && $end_info[] = "\t路由加控制器用时: " . (string) round(ROUTE_CONTROLLER_END - ROUTE_CONTROLLER_START, 12) . '秒';
            $end_info[] = "\t内存峰值: " . base\number\Number::byte(memory_get_peak_usage(), false);
        }

        return $end_info;
    }
    private static function getFullInfo() : array
    {
        defined('PHP_START_TIME') && $end_time = microtime(true);
        defined('PHP_START_MEM')  && $end_mem = memory_get_usage();
        defined('PHP_START_CPU')  && $end_cpu = getrusage();

        $end_info = [];

        if (isset($end_time)) {
            $end_info[] = '时间信息:';
            $end_info[] = "\t现在时间戳      : " . (string) round(microtime(true)                , 12) . '秒';
            $end_info[] = "\t现在时间        : " . date('Y-m-d H:i:s');
            $end_info[] = "\t总用时          : " . (string) round($end_time      - PHP_START_TIME , 12) . '秒';
            defined('ROUTE_CONTROLLER_END') && $end_info[] = "\t框架实际用时    : " . (string) round($end_time      - PHP_START_TIME - ROUTE_CONTROLLER_END + ROUTE_CONTROLLER_START, 12) . '秒';
            defined('CRON_START')           && $end_info[] = "\t定时任务用时    : " . (string) round(CRON_END - CRON_START, 12) . '秒';
            defined('ROUTE_CONTROLLER_END') && $end_info[] = "\t路由加控制器用时: " . (string) round(ROUTE_CONTROLLER_END - ROUTE_CONTROLLER_START, 12) . '秒';
            unset($end_time);
        }

        if (isset($end_mem)) {
            $end_info[] = '内存信息:';
            $end_info[] = "\t开始内存: " . base\number\Number::byte(PHP_START_MEM, false);
            $end_info[] = "\t结束内存: " . base\number\Number::byte($end_mem, false);
            $end_info[] = "\t内存差值: " . base\number\Number::byte($end_mem-PHP_START_MEM, false);
            $end_info[] = "\t内存峰值: " . base\number\Number::byte(memory_get_peak_usage(), false);
            unset($end_mem);
        }

        if (function_exists('get_defined_constants')) {
            $end_info[] = '常量信息:';
            foreach (get_defined_constants(true)['user'] as $key => $value) {
                $end_info[] = "\t".$key."\t=>\t".var_export($value, true);
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
            unset($file_info, $files, $all_size, $file, $byte);
        }
        $composer = core\loader\Loader::getLoadedClasses();

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