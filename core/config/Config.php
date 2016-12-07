<?php declare(strict_types = 1);
namespace msqphp\core\config;

use msqphp\base;
use msqphp\core\traits;

final class Config
{
    use traits\Instance;

    // 配置
    private $config = [];

    // 初始化
    private function __construct()
    {
        // 配置缓存路径
        $cache_path = \msqphp\Environment::getPath('storage') . 'framework' . DIRECTORY_SEPARATOR . 'cache_config.php';

        // 有缓存且文件存在,直接载入
        if (HAS_CACHE && is_file($cache_path)) {
            // 直接载入
            $this->config = require $cache_path;
        } else {
            // 加载全部                          获得文件列表
            array_map([$this, 'loadConfigFIle'], base\dir\Dir::getFileList(\msqphp\Environment::getPath('config'), true));
            // 写入
            HAS_CACHE && base\file\File::write($cache_path, '<?php return '.var_export($this->config, true).';');
        }
    }

    // 抛出异常
    private function exception(string $message) : void
    {
        throw new ConfigException($message);
    }

    /**
     * @param  string $key   键
     * @param  miexd  $value 值
     */

    // 获取配置
    public function get(?string $key = null)
    {
        return base\arr\Arr::get($this->config, $key);
    }

    // 设置配置
    public function set(?string $key, $value) : void
    {
        base\arr\Arr::set($this->config, $key, $value);
    }

    // 加载配置文件
    private function loadConfigFIle(string $file) : void
    {
        is_readable($file) || $this->exception($file . '配置文件无法加载');

        $this->config[pathinfo($file, PATHINFO_FILENAME)] = require $file;
    }
}