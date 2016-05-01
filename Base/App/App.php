<?php declare(strict_types = 1);
namespace Core\Base\App;

use Core\Base;

class App
{
    //控制器对象
    static private $cont              = null;
    //控制器缓存键
    static private $cont_key          = '';
    //控制器缓存值
    static private $cont_info         = [];
    //缓存类处理器
    static private $cache_handler     = null;

    //保存app信息(array('controller'     =>'Index','method'=>'login'[,'language'=>'zh-cn'[,'module'=>'home']]));
    static public  $info              = [];
    //盐
    static public  $salt              = '';
    
    //路径信息
    static public  $module_path       = '';
    static public  $cont_path         = [];
    
    /**
     * app初始化
     * @return void
     */
    static public function init(array $info,array $config = [])
    {
        $config                = $config ?: require \Core\Framework::$config_path.'app.php';
        static::$info          = $info;
        static::$salt          = $config['salt'] ?? md5(__FILE__);
        $app_path              = $config['path'] ?? \Core\Framework::$app_path;
        static::$module_path   = $module_path = $app_path . (isset($info['module']) ? $info['module'].DIRECTORY_SEPARATOR : '');
        static::$cont_path     = $module_path . $config['controller_layer'] . DIRECTORY_SEPARATOR;
        static::$cache_handler = Base\Cache\Cache::getInstance()->getDriver($config['cache_handler']);
    }
    /**
     * app开始
     * @return void
     */
    static public function start() {
        //初始化控制器
        static::initCont();
    }
    /**
     * 得到控制器缓存信息
     * @return void
     */
    static public function initCont()
    {

        //组成缓存键值
        $info     = static::$info;
        $cont_key = md5($info['controller'].$info['method']);
        static::$cont_key = $cont_key;
        //获得信息
        $cont_info = [];
        //缓存存在
        if (true === static::$cache_handler->available($cont_key)) {
            //取值
            $cont_info = static::$cache_handler->get($cont_key);
            //载入文件
            require __DIR__.'/../Controller/Controller.php';
            require $cont_info['file'];
            //创建类
            $cont       = new $cont_info['class_name'] ();
        } else {
        //拼接
            $controller = $info['controller'].'Controller';
            $method     = $info['method'];
            
            //拼接文件路径
            $cont_file = static::$cont_path.$controller.'.php';
            if (!is_file($cont_file)) {
                throw new AppException($controller.'控制器不存在');
            }
            
            //载入控制器类
            require __DIR__.'/../Controller/Controller.php';
            require $cont_file;
            //2.创建类
            $module     = isset($info['module']) ? '\\'.$info['module'] : '';
            $class_name = '\\App'.$module.'\\Controller\\'.$controller;

            $cont = new $class_name();
            //3.判断方法是否存在
            if (!method_exists($cont,$method)) {
                throw new AppException($method.'方法不存在', 501);
            }
            //4.添加缓存信息
            $cont_info['file']          = $cont_file;
            $cont_info['class_name']    = $class_name;
        }
        //赋值
        static::$cont      = $cont;
        static::$cont_info = $cont_info;
    }
    /**
     * app运行
     * @return void
     */

    static public function run()
    {
        $method = static::$info['method'];
        define('PHP_CONT_START',microtime(true));
        static::$cont-> $method();
        define('PHP_CONT_END',microtime(true));
        static::$cont = null;
    }
    /**
     * app结束
     * @return void
     */
    static public function end()
    {
        if (!isset(static::$cont_info['cached'])) {
            $cont_info = static::$cont_info;
            $cont_info['cached'] = true;
            static::$cache_handler->set(static::$cont_key, $cont_info,3600);
        }
    }
}