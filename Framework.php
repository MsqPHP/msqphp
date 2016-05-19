<?php declare(strict_types = 1);
namespace msqphp;

class Framework
{
    public static function install(string $root)
    {
        require_once __DIR__.'/../../autoload.php';
        $root = realpath($root) . DIRECTORY_SEPARATOR;
        $lib_path = $root . 'library' . DIRECTORY_SEPARATOR . 'msqphp' . DIRECTORY_SEPARATOR . 'framework';
        $path_config = [
            'root'        => $root,
            'application' => $root . 'application',
            'resources'   => $root . 'resources',
            'bootstrap'   => $root . 'bootstrap',
            'config'      => $root . 'config',
            'public'      => $root . 'public',
            'storage'     => $root . 'storage',
            'library'     => $lib_path,
        ];
        foreach ($path_config as $key => $path) {
            base\dir\Dir::make($path, true);
            if ($key === 'public' || base\dir\Dir::isEmpty($path, true)) {
                base\dir\Dir::copy(__DIR__.DIRECTORY_SEPARATOR.'resource'.DIRECTORY_SEPARATOR.$key, $path, true);
            }
        }
        $dir_list = base\dir\Dir::getAllDir(__DIR__);
        foreach ($dir_list as $dir) {
            if (base\str\Str::endsWith($dir, ['methods', 'gets', 'staticMethods', 'handlers', 'drivers'])) {
                $dir = str_replace(__DIR__, $lib_path, $dir);
                base\dir\Dir::make($dir, true);
            }
        }
    }
}