<?php declare(strict_types = 1);
namespace msqphp\main\cache;

trait CacheStaticTrait
{
    // 所有处理类
    private static $handlers   = [];

    // 缓存配置
    private static $config     = [
        'multiple'             =>  false,
        'sports'               =>  ['File'],
        'default_handler'      =>  'File',
        'prefix'               =>  '',
        'expire'               =>  3600,
        // 处理类配置
        'handlers_config'      =>  [],
    ];

    // 初始化静态类
    private static function initStatic() : void
    {
        // 初始化过直接返回
        static $inited = false;
        if (!$inited) {
            $inited = true;
            static::initConfig();
            // 载入默认处理器接口文件
            require __DIR__ . DIRECTORY_SEPARATOR . 'handlers' . DIRECTORY_SEPARATOR . 'CacheHandlerInterface.php';
        }
    }
    // 初始化配置
    private static function initConfig() : void
    {
        // 缓存配置
        $config           = array_merge(static::$config, app()->config->get('cache'));

        // 处理类支持列表
        $config['sports'] = $config['multiple'] ? $config['sports'] : [ $config['default_handler'] ];

        // 赋值配置
        static::$config     = $config;
    }

    // 获得默认的缓存处理器
    private static function getDefaultHandler() : handlers\CacheHandlerInterface
    {
        return static::getCacheHandler(static::$config['default_handler'], static::$config['handlers_config'][static::$config['default_handler']]);
    }

    /**
     * 获得对应的处理类
     *
     * @param   string  $type    处理器种类
     * @param   array   $config  处理器配置
     *
     * @return  handlers\CacheHandlerInterface
     */
    private static function getCacheHandler(string $type, array $config = []) : handlers\CacheHandlerInterface
    {
        // 为空取默认
        $type = ucfirst($type ?: static::$config['default_handler']);

        // 支持或异常
        in_array($type, static::$config['sports']) || static::exception($type . ' 缓存处理器并不支持');

        // 配置合并
        $config               = array_merge(static::$config['handlers_config'][$type], $config);

        // 获取一个键,代表该处理器在所有处理器中的一个键 若为配置中的,则直接取type,否则取一个md5键
        $key                  = empty($config) ? md5($type) : md5(serialize($config).$type);

        // 存在直接返回,否者获得一个实例
        return static::$handlers[$key] = static::$handlers[$key] ?? static::initCacheHandler($type, $config);
    }

    /**
     * 获得一个处理类实例
     *
     * @param   string  $type    处理器种类
     * @param   array   $config  处理器配置
     *
     * @return  handlers\CacheHandlerInterface
     */
    private static function initCacheHandler(string $type, array $config) : handlers\CacheHandlerInterface
    {
        // 一个数组,包括所有载入过的处理器文件
        static $files  = [];

        // 处理类文件是否载入
        if (!isset($files[$type])) {
            $file = __DIR__.DIRECTORY_SEPARATOR.'handlers'.DIRECTORY_SEPARATOR.$type.'.php';
            // 如果不存在查找lib目录下是否存在
            is_file($file) || $file = str_replace(\msqphp\Environment::getPath('library'), \msqphp\Environment::getPath('framework'), $file);
            // 仍不存在,异常
            is_file($file) || static::exception($type.'缓存处理类不存在');
            // 载入文件
            require $file;
            // 文件载入过
            $files[$type] = true;
        }
        // 拼接类名
        $class = __NAMESPACE__.'\\handlers\\'.$type;
        // 创建类
        return new $class($config);
    }
}