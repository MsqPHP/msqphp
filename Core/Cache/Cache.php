<?php declare(strict_types = 1);
namespace Msqphp\Core\Cache;

class Cache
{
    //缓存配置
    private $config          = [];
    //默认驱动
    private $driver          = null;
    //默认驱动名称
    private $default_driver  = '';
    //所有驱动
    private $drivers         = [];
    //是否支持多驱动
    private $multi_cache     = false;
    //支持类型
    private $sport_list      = [];
    //缓存前缀
    private $prefixion       = '';
    //当前驱动对象
    private static $instance = null;


    private function __construct(array $config = [])
    {
        //获得当前对象
        $cache = $this;
        //缓存设置
        $cache->config = $config = $config ?: require \Msqphp\Environment::$config_path.'cache.php';
        //默认缓存驱动
        $cache->default_driver = $default_driver = $config['default_driver'];
        //缓存驱动接口文件
        require __DIR__.DIRECTORY_SEPARATOR.'CacheInterface.php';
        //加载驱动
        $cache->driver = $cache->initDriver($default_driver);
        //如果支持多驱动
        if ($config['multi_driver']) {
            $cache->multi_cache = true;
            $cache->sport_list = $config['sport_list'];
            $cache->drivers[$default_driver] = $cache->driver;
        }
    }
    /**
     * 得到缓存实例
     * @return [type]
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new Cache();
        }
        return static::$instance;
    }
    /**
     * 得到对应的驱动
     * @param  string $type 驱动类型
     * @throws Exception
     * @return 驱动
     */
    public function getDriver(string $type) : CacheInterface
    {
        //获得当前对象
        $cache = $this;

        if ($type === '' || $type === $cache->default_driver) {
            return $cache->driver;

        //是否支持多驱动，且驱动名称在支持驱动列表中
        } elseif ($cache->multi_cache && in_array($type,$cache->sport_list)) {

            if(!isset($cache->drivers[$type])) {
                $cache->drivers[$type] = $cache->initDriver($type);
            }
            return $cache->drivers[$type];
        } else {
            throw new CacheException($type . 'cache driver 配置并不支持');
        }
        return $driver;
    }
    /**
     * 加载驱动
     * @param  string $type  驱动类型
     * @return 驱动
     */
    private function initDriver(string $type)
    {
        //载入驱动类
        require __DIR__.DIRECTORY_SEPARATOR.'Driver'.DIRECTORY_SEPARATOR.$type.'.php';

        //获得对应文件
        $driver_config = $this->config['driver_config'][$type];
        //拼接类名
        $class_name = __NAMESPACE__.'\\Driver\\'.$type;
        //创建类
        return new $class_name($driver_config);
    }
}