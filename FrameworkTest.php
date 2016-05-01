<?php declare(strict_types = 1);
namespace Core;

class FrameworkTest extends \Core\Test\Test
{
    //根目录
    static public $root_path   = '';
    //公共资源目录
    static public $public_path = '';
    //配置目录
    static public $config_path = '';
    //应用目录
    static public $app_path    = '';

    public function testStart()
    {
        $this->testThis($this);
    }

    public function testInit()
    {
        $root = __DIR__.DIRECTORY_SEPARATOR;
        $path_config = [
            'root'=>$root,
            'application'=>$root.'application',
            'config'=>$root.'config',
            'public'=>$root.'public',
        ];
        $this->testStaticMethod('\Core\Framework::init',array($path_config),null);

        $this->checkStaticProperty('\Core\Framework','root_path',$root);
        (is_dir($path_config['application']) && $app_path = $root.'application') || ($app_path = DIRECTORY_SEPARATOR);
        (is_dir($path_config['config']) && $config_path = $root.'config') || ($config_path = DIRECTORY_SEPARATOR);
        (is_dir($path_config['public']) && $public_path = $root.'public') || ($public_path = DIRECTORY_SEPARATOR);
        $this->checkStaticProperty('\Core\Framework','public_path',$public_path);
        $this->checkStaticProperty('\Core\Framework','config_path',$app_path);
        $this->checkStaticProperty('\Core\Framework','app_path',$config_path);

        $root = realpath(__DIR__.'/../../');

        $path_config = [
            'root'=>$root,
            'application'=>$root.'application',
            'config'=>$root.'config',
            'public'=>$root.'public',
        ];
        $this->testStaticMethod('\Core\Framework::init',array($path_config),null);

        $this->checkStaticProperty('\Core\Framework','root_path',$root);
        (is_dir($path_config['application']) && $app_path = $root.'application') || ($app_path = DIRECTORY_SEPARATOR);
        (is_dir($path_config['config']) && $config_path = $root.'config') || ($config_path = DIRECTORY_SEPARATOR);
        (is_dir($path_config['public']) && $public_path = $root.'public') || ($public_path = DIRECTORY_SEPARATOR);
        $this->checkStaticProperty('\Core\Framework','public_path',$public_path);
        $this->checkStaticProperty('\Core\Framework','config_path',$app_path);
        $this->checkStaticProperty('\Core\Framework','app_path',$config_path);
        return true;
    }
}