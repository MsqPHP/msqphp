<?php declare(strict_types = 1);
namespace Msqphp;

class EnvironmentTest extends \Msqphp\Test\Test
{
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
            'library'=>$root.'library',
            'resources'=>$root.'resources',
        ];
        $this->testStaticMethod('\Msqphp\Environment::init',array($path_config),null);

        $this->checkStaticProperty('\Msqphp\Environment','root_path',$root);
        (is_dir($path_config['application']) && $app_path = $root.'application') || ($app_path = DIRECTORY_SEPARATOR);
        (is_dir($path_config['config']) && $config_path = $root.'config') || ($config_path = DIRECTORY_SEPARATOR);
        (is_dir($path_config['public']) && $public_path = $root.'public') || ($public_path = DIRECTORY_SEPARATOR);
        (is_dir($path_config['library']) && $library_path = $root.'library') || ($public_path = DIRECTORY_SEPARATOR);
        (is_dir($path_config['resources']) && $resources_path = $root.'resources') || ($public_path = DIRECTORY_SEPARATOR);
        $this->checkStaticProperty('\Msqphp\Environment','public_path',$public_path);
        $this->checkStaticProperty('\Msqphp\Environment','config_path',$app_path);
        $this->checkStaticProperty('\Msqphp\Environment','app_path',$config_path);

        $root = realpath(__DIR__.'/../../../');

        $path_config = [
            'root'=>$root,
            'application'=>$root.'application',
            'config'=>$root.'config',
            'public'=>$root.'public',
        ];
        $this->testStaticMethod('\Msqphp\Environment::init',array($path_config),null);

        $this->checkStaticProperty('\Msqphp\Environment','root_path',$root);
        (is_dir($path_config['application']) && $app_path = $root.'application') || ($app_path = DIRECTORY_SEPARATOR);
        (is_dir($path_config['config']) && $config_path = $root.'config') || ($config_path = DIRECTORY_SEPARATOR);
        (is_dir($path_config['public']) && $public_path = $root.'public') || ($public_path = DIRECTORY_SEPARATOR);
        $this->checkStaticProperty('\Msqphp\Environment','public_path',$public_path);
        $this->checkStaticProperty('\Msqphp\Environment','config_path',$app_path);
        $this->checkStaticProperty('\Msqphp\Environment','app_path',$config_path);
        return true;
    }
}