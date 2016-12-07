<?php declare(strict_types = 1);
namespace msqphp\core\cli;

class CliFramework
{
    public static function install(string $root) : void
    {
        //根目录
        $root = realpath($root) . DIRECTORY_SEPARATOR;

        //lib目录
        $lib_path = $root . 'library' . DIRECTORY_SEPARATOR . 'msqphp' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR;

        //框架目录
        $framework_path = realpath(__DIR__.'/../../').DIRECTORY_SEPARATOR;

        //安装资源目录
        $install_path = $framework_path.DIRECTORY_SEPARATOR.'resource'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR;

        //目录配置
        $path_config = [
            'root'        => $root,
            'application' => $root . 'application',
            'applicationtest' => $root . 'applicationtest',
            'resources'   => $root . 'resources',
            'bootstrap'   => $root . 'bootstrap',
            'config'      => $root . 'config',
            'public'      => $root . 'public',
            'storage'     => $root . 'storage',
            'library'     => $lib_path,
        ];
        //复制
        foreach ($path_config as $key => $path) {
            base\dir\Dir::make($path, true);
            if ('public' === $key || base\dir\Dir::isEmpty($path, true)) {
                base\dir\Dir::copy($install_path.$key, $path, true);
            }
            chmod($path, ('public' === $key || 'storage' === $key) ? 0777 : 0755);
        }

        //lib目录对应目录创建
        array_map(function (string $dir) use ($lib_path) {
            if (base\str\Str::endsWith($dir, ['methods', 'gets', 'staticMethods', 'handlers', 'drivers', 'binds'])) {
                base\dir\Dir::make(str_replace($framework_path.DIRECTORY_SEPARATOR, $lib_path, $dir), true);
            }
        }, base\dir\Dir::getAllDir($framework_path));
    }
}