<?php declare(strict_types = 1);
namespace Core\Test\Base\App;

use Core\Base;

class AppTest extends \Core\Test\Test
{
    
    //控制器对象
    static private $cont              = null;
    //缓存类对象
    static private $cache             = null;
    //保存app信息(array('controller'     =>'Index','method'=>'login'[,'language'=>'zh-cn'[,'module'=>'home']]));
    static public  $info              = [];
    //控制器缓存信息
    static private  $cont_info        = [];
    static private $cont_key          = '';
    //盐
    static public  $salt              = '';
    //路径信息
    static public  $module_path       = '';
    static public  $cont_path         = [];
    static public  $lang_path         = '';
    
    public function testStart()
    {
        $this->testThis();
    }
    public function testInit()
    {
        $info = array(
            'module'=>'Home',
            'language'=>'zh-cn',
            'controller'=>'index',
            'method'=>'index',
            'app_path'=>__DIR__.DIRECTORY_SEPARATOR,
        );
        $config = array( 
            //默认加密盐
            'salt'              =>  '87923e71dbee64e957fc132f276f04bf',
            /* 默认设定 */
            'module_layer'      =>  'module',
            'controller_layer'  =>  'Controller', // 默认的控制器文件夹名称
            'test_layer'        =>  'Test', // 默认的模版文件夹名称
            'cache_driver'      =>  'Memcached',//cont数据缓存
        );

        $this->setStaticProperty('\Core\Framework','config_path',__DIR__.'/../../../../../config/');
        $this->testStaticMethod('\Core\Base\App\App::init',array($info,$config),null);

        $this->checkStaticProperty('\Core\Base\App\App','info',$info);
        $this->checkStaticProperty('\Core\Base\App\App','salt','87923e71dbee64e957fc132f276f04bf');
        $this->checkStaticProperty('\Core\Base\App\App','module_path',__DIR__.DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.'Home'.DIRECTORY_SEPARATOR);
        $this->checkStaticProperty('\Core\Base\App\App','cont_path',__DIR__.DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR.'Home'.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR);
        return true;
    }
}