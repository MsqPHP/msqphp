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
        static::initAutoLoader();
        static::initRealWithAiload();
    }
    /**
     * loader静态类储存一个数组
     * 包括所有通过自动加载加载的文件(框架自带或者composer[需要改动代码])
     * 以实现智能加载
     * 所以在载入自动加载类前先载入对应文件
     */
    private static function initAutoLoader() : void
    {
        $framework_path = static::getPath('framework');
        if (COMPOSER_AUTOLOAD) {
            // 载入简易加载类文件
            require $framework_path . 'core/loader/BaseTrait.php';
            require $framework_path . 'core/loader/AiloadTrait.php';
            require $framework_path . 'core/loader/SimpleLoader.php';
            //使用则载入composer自动加载类
            require static::getPath('root') . 'vendor/autoload.php';
        } else {
            // 载入完整加载类文件
            require $framework_path . 'core/loader/BaseTrait.php';
            require $framework_path . 'core/loader/AutoloadTrait.php';
            require $framework_path . 'core/loader/AiloadTrait.php';
            require $framework_path . 'core/loader/Loader.php';
            core\loader\Loader::register();
        }
    }

    // 实际的初始化函数
    private static function initReal() : void
    {
        // 错误处理
        core\wrong\Wrong::init();
        // 时区设置
        date_default_timezone_set(app()->config->get('framework.timezone'));
    }

    // 真正的环境初始化,配合aiload使用
    private static function initRealWithAiload() : void
    {
        //智能加载缓存文件
        $aiload_cache_file = static::getPath('storage') . 'framework/aiload_cache_file.php';

        //缓存文件存在
        if (is_file($aiload_cache_file)) {
            //载入对应缓存
            include $aiload_cache_file;
            //初始化
            static::initReal();
            //随机值删除
            random_int(1, 1000000) === 1000 && msqphp\base\file\File::delete($aiload_cache_file, true);
        } else {
            //创建一个新缓存
            $loader = app()->loader;
            $loader->key('environment')->load();

            //初始化
            static::initReal();

            // 如果为最终,初始化,删除
            if ($loader->last()) {
                $needful_classes = $loader->getLastNeedfulClasses();
                msqphp\base\file\File::write($aiload_cache_file, empty($needful_classes) ? '' : '<?php include \'' . implode('\';include \'', $loader->getLastNeedfulClasses()) . '\';', true);
            // 删除全部
            } else {
                $loader->deleteAll();
                $loader->update();
            }
        }
    }

    // 获取当前运行环境
    public static function getRunMode() : string
    {
        if (static::$run_mode !== '') {
            return static::$run_mode;
        }
        switch (PHP_SAPI) {
            case 'cli':
                return static::$run_mode = 'cli';
            case 'cgi':
            case 'cgi-fcgi':
            case 'apache':
            case 'apache2filter':
            case 'apache2handler':
            default :
                return static::$run_mode = 'web';
        }
    }

    // 设置路径
    public static function setPath(array $path_config) : void
    {
        foreach ($path_config as $name => $path) {
            // 存在或报错
            if (!is_dir($path)) {
                throw new \Exception('框架环境初始化错误,原因:' . $path . '目录不存在');
            }
            // 是目录则realpath
            isset(static::$path[$name]) && static::$path[$name] = realpath($path) . DIRECTORY_SEPARATOR;
        }
    }

    // 获取路径
    public static function getPath(string $name) : string
    {
        if (!isset(static::$path[$name])) {
            throw new \Exception('目标路径无法获取' . $name);
        }
        return static::$path[$name];
    }
}