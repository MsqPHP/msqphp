<?php declare(strict_types = 1);
namespace msqphp;

class Framework
{
    public static function install(string $root)
    {
        //引用自动加载类
        require_once __DIR__.'/../../autoload.php';

        //根目录
        $root = realpath($root) . DIRECTORY_SEPARATOR;

        //lib目录
        $lib_path = $root . 'library' . DIRECTORY_SEPARATOR . 'msqphp' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR;

        //安装资源目录
        $install_path = __DIR__.DIRECTORY_SEPARATOR.'resource'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR;

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
            if ('public' === $key || 'storage' === $key) {
                chmod($path, 0666);
            } else {
                chmod($path, 0444);
            }
        }

        //lib目录对应目录创建
        array_map(function (string $dir) use ($lib_path) {
            if (base\str\Str::endsWith($dir, ['methods', 'gets', 'staticMethods', 'handlers', 'drivers'])) {
                $dir = str_replace(__DIR__.DIRECTORY_SEPARATOR, $lib_path, $dir);
                base\dir\Dir::make($dir, true);
            }
        }, base\dir\Dir::getAllDir(__DIR__));
    }
}