<?php declare(strict_types = 1);
namespace msqphp;

final class Environment
{
    // 框架版本
    private const vension = 2.0;
    // 运行模式
    private static $run_mode = '';

    // 所有目录存放
    private static $path = [
        'application' => '',
        'bootstrap'   => '',
        'config'      => '',
        'library'     => '',
        'public'      => '',
        'resources'   => '',
        'root'        => '',
        'storage'     => '',
        'test'        => '',
        'framework'   => ''
    ];

    // 初始化框架环境
    public static function init() : void
    {
        core\error\Error::init();
        // 时区设置
        date_default_timezone_set(app()->config->get('framework.timezone'));

        define('CRON_START', microtime(true));
        // 运行定时任务
        core\cron\Cron::run();
        define('CRON_END', microtime(true));
    }

    // 获取当前运行环境
    public static function getRunMode() : string
    {
        if (static::$run_mode === '') {
            switch (PHP_SAPI) {
                case 'cli':
                    static::$run_mode = 'cli';
                    break;
                case 'cgi':
                case 'cgi-fcgi':
                case 'apache':
                case 'apache2filter':
                case 'apache2handler':
                default :
                    static::$run_mode = 'web';
            }
        }
        return static::$run_mode;
    }

    /**
     * 设置路径
     *
     * @param  array $path_config 路径配置
     *
     * @return void
     */
    public static function setPath(array $path_config) : void
    {
        // 初始化路径
        static::$path = array_map(function (string $path) : string {

            // 存在或报错
            if (!is_dir($path)) {
                throw new \Exception('框架环境初始化错误,原因:' . $path . '目录不存在');
            }

            // 是目录则realpath
            return realpath($path) . DIRECTORY_SEPARATOR;

        }, $path_config);
    }

    /**
     * 获取路径
     *
     * @param  string $name 名称
     *
     * @return string
     */
    public static function getPath(string $name) : string
    {
        if (!isset(static::$path[$name])) {
            throw new \Exception('目标路径无法获取' . $name);
        }
        return static::$path[$name];
    }
}