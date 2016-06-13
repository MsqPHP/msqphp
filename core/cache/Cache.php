<?php declare(strict_types = 1);
namespace msqphp\core\cache;

use msqphp\core;
use msqphp\traits;

final class Cache
{
    //单例模式
    use traits\Instance;
    //指针函数
    use CachePointerTrait;
    //操作函数
    use CacheOperateTrait;

    //所有处理类
    private $handlers   = [];

    //缓存配置
    private $config     = [
        'multi'                =>  false,
        'sports'               =>  ['File'],
        'default_handler'      =>  'File',
        'prefix'               =>  '',
        'expire'               =>  3600,
        //处理类配置
        'handlers_config'      =>  [],
    ];


    /**
     * 构造方法 $config
     *
     * @return  void
     */
    private function __construct()
    {
        //缓存配置
        $config           = array_merge($this->config, core\config\Config::get('cache'));

        //处理类支持列表
        $config['sports'] = $config['multi'] ? $config['sports'] : [ $config['default_handler'] ];

        //赋值配置
        $this->config     = $config;

        //缓存处理类接口文件
        require __DIR__.DIRECTORY_SEPARATOR.'handlers'.DIRECTORY_SEPARATOR.'CacheHandlerInterface.php';

        //设置处理类
        $this->setHandler($config['default_handler']);
    }
    /**
     * 设置对应的处理类
     *
     * @param  string $type    处理类类型
     * @param  array  $config  处理类配置
     *
     * @throws CacheException
     * @return handlers\CacheHandlerInterface
     */
    private function setHandler(string $type = '', array $config = []) : handlers\CacheHandlerInterface
    {
        //为空取默认
        $type = ucfirst($type ?: $this->config['default_handler']);
        //如果不在支持列表中
        if (!in_array($type, $this->config['sports'])) {
            throw new CacheException($type . ' 缓存处理器不支持');
        }

        //获得键
        $key                  = empty($config) ? md5($type) : md5(serialize($config).$type);
        //配置
        $config               = array_merge($this->config['handlers_config'][$type], $config);

        return $this->handlers[$key] = $this->handlers[$key] ?? $this->initHandler($type, $config);
    }
    /**
     * 加载并返回处理类
     *
     * @param  string $type    处理类类型
     * @param  array  $config  处理类配置
     *
     * @throws CacheException
     * @return handlers\CacheHandlerInterface
     */
    private function initHandler(string $type, array $config) : handlers\CacheHandlerInterface
    {
        static $files  = [];

        if (!isset($files[$type])) {
            //载入cache处理类文件
            $file = __DIR__.DIRECTORY_SEPARATOR.'handlers'.DIRECTORY_SEPARATOR.$type.'.php';
            //如果不存在查找lib目录下是否存在
            is_file($file) || $file = str_replace(\msqphp\Environment::getPath('library'), \msqphp\Environment::getPath('framework'), $file);

            if (!is_file($file)) {
                throw new CacheException($type.'缓存处理类不存在');
            }

            require $file;
            $files[$type] = true;
        }

        //拼接类名
        $class = __NAMESPACE__.'\\handlers\\'.$type;
        //创建类
        return new $class($config);
    }
}