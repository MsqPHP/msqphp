<?php declare(strict_types = 1);
namespace msqphp;

final class Cli
{
    private static $script = '';
    private static $args = [];
    private static $php_path = '';

    private static function exception(string $message) : void
    {
        throw new \Exception($message);
    }

    private static function getPhpPath() : string
    {
        if (static::$php_path !== '') {
            return static::$php_path;
        }
        return static::$php_path = strtolower(substr(PHP_OS, 0, 3)) === 'win'
        // 根据扩展目录,获得php根目录以确定php位置
        ? dirname(realpath(ini_get('extension_dir'))) . DIRECTORY_SEPARATOR . 'php.exe'
        // 直接根据bin目录确定
        : PHP_BINDIR . '/php';
    }
    public static function runPhpFile(string $file, array $params = []) : void
    {
        exec(static::getPhpPath() . ' '. $file . ' ' . implode(' ', $params));
    }
    public static function run()
    {
        static::$script = array_shift($GLOBALS['argv']);
        static::$args = $GLOBALS['argv'];
        switch (array_shift(static::$args)) {
            case 'framework':
                static::framework();
                break;
            case 'cron':
                static::cron();
                break;
            case 'queue':
                static::queue();
                break;
            case 'test':
                require __DIR__.'/test.php';
                break;
            default:
                static::exception('未知的cli命令');
        }
    }
    private static function framework()
    {
        switch (array_shift(static::$args)) {
            case 'install':
                core\cli\CliFramework::install(static::$args[0]);
                break;
        }
    }
    private static function cron()
    {
        switch (array_shift(static::$args)) {
            case 'run':
            case 'start':
                core\cli\CliCron::run();
                break;
            case 'stop':
            case 'end':
                core\cli\CliCron::stop();
                break;
            case 'rerun':
                core\cli\CliCron::rerun();
                break;
            case 'status':
                core\cli\CliCron::status();
                break;
            default:
                static::exception('未知的cli命令');
        }
    }
    private static function queue()
    {
        switch (array_shift(static::$args)) {
            case 'run':
                core\cli\CliQueue::run();
                break;
            case 'stop':
                core\cli\CliQueue::stop();
                break;
            case 'rerun':
                core\cli\CliQueue::rerun();
                break;
            case 'status':
                core\cli\CliQueue::status();
                break;
            default:
                static::exception('未知的cli命令');
        }
    }

    public static function forever(\Closure $func, array $params = []) : void
    {
        set_time_limit(0);
        $bool = true;
        while ($bool) {
            $bool = true && call_user_func_array($func, $params);
        }
    }
    public static function memory_limit(int $size, \Closure $func, array $params = []) : void
    {
        if ($size < memory_get_usage(true)) {
            call_user_func_array($func, $params);
        }
    }
}